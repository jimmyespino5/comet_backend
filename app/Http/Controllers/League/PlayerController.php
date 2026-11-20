<?php
namespace App\Http\Controllers\League;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Team;
use App\Models\TournamentRegistration;
use App\Models\TournamentRoster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PlayerController extends Controller
{
    /**
     * Listar todos los jugadores registrados con soporte para búsqueda y paginación.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Player::with('user');

        // Filtro por búsqueda (Nombre, Apellido o DNI)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%");
            });
        }

        // Filtro opcional por posición
        if ($request->has('position') && !empty($request->position)) {
            $query->where('position', $request->position);
        }

        $players = $query->orderBy('last_name', 'asc')->paginate(15);

        return response()->json($players);
    }

    /**
     * Crear un nuevo jugador en el sistema.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id'    => 'nullable|exists:users,id|unique:players,user_id',
            'dni'        => 'required|string|max:20|unique:players,dni',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // 2MB máximo
            'position'   => 'nullable|in:goalkeeper,defender,winger,pivot',
        ]);

        // Procesar subida de foto si se envió
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('players', 'public');
            $validated['photo_path'] = $path;
        }

        $player = Player::create($validated);

        return response()->json([
            'message' => 'Jugador registrado exitosamente.',
            'player'  => $player->load('user')
        ], 201);
    }

    /**
     * Ver el perfil completo de un jugador e historial de torneos en los que ha participado.
     */
    public function show(Player $player): JsonResponse
    {
        $player->load([
            'user',
            'rosters.registration.tournament',
            'rosters.registration.team',
            'goals',
            'sanctions'
        ]);

        return response()->json($player);
    }

    /**
     * Actualizar los datos del jugador.
     */
    public function update(Request $request, Player $player): JsonResponse
    {
        $validated = $request->validate([
            'user_id'    => 'nullable|exists:users,id|unique:players,user_id,' . $player->id,
            'dni'        => 'required|string|max:20|unique:players,dni,' . $player->id,
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'photo'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'position'   => 'nullable|in:goalkeeper,defender,winger,pivot',
        ]);

        // Manejo de reemplazo de foto
        if ($request->hasFile('photo')) {
            // Eliminar la foto antigua si existe
            if ($player->photo_path && Storage::disk('public')->exists($player->photo_path)) {
                Storage::disk('public')->delete($player->photo_path);
            }

            $path = $request->file('photo')->store('players', 'public');
            $validated['photo_path'] = $path;
        }

        $player->update($validated);

        return response()->json([
            'message' => 'Datos del jugador actualizados correctamente.',
            'player'  => $player->load('user')
        ]);
    }

    /**
     * Eliminar un jugador del sistema.
     */
    public function destroy(Player $player): JsonResponse
    {
        // Si tiene una foto guardada en disco, la borramos
        if ($player->photo_path && Storage::disk('public')->exists($player->photo_path)) {
            Storage::disk('public')->delete($player->photo_path);
        }

        $player->delete();

        return response()->json([
            'message' => 'Jugador eliminado del sistema.'
        ]);
    }

    /**
     * Crear o buscar un jugador e inscribirlo en la plantilla del equipo.
     */
    public function storeForTeam(Request $request, Team $team): JsonResponse
    {
        $validated =$request->validate([
            'dni'           => 'required|string|max:20',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'birth_date'    => 'nullable|date',
            'position'      => 'nullable|in:goalkeeper,defender,winger,pivot',
            'photo'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'jersey_number' => 'nullable|integer|min:1|max:99',
            'tournament_id' => 'required|exists:tournaments,id',
        ]);

        return DB::transaction(function () use ($request,$validated, $team) { // 1. Manejo de la foto si fue subida$photoPath = null;
        $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath =$request->file('photo')->store('players', 'public');
            }

            // 2. Buscar si el jugador ya existe por su DNI o crearlo (Tabla `players`)
            $player = Player::firstOrCreate(
                ['dni' => $validated['dni']],
                [
                    'first_name' => $validated['first_name'],
                    'last_name'  => $validated['last_name'],
                    'birth_date' => $validated['birth_date'] ?? null,
                    'position'   => $validated['position'] ?? null,
                    'photo_path' => $photoPath,
                ]
            );

            // 3. Buscar la inscripción del equipo en ese torneo
            $registration = TournamentRegistration::where('team_id',$team->id)
                ->where('tournament_id', $validated['tournament_id'])
                ->first();

            if (!$registration) {
                return response()->json([
                    'message' => 'El equipo no está inscrito en el torneo especificado.'
                ], 422);
            }

            // 4. Inscribir al jugador en la plantilla (Tabla `tournament_rosters`)
            $roster = TournamentRoster::updateOrCreate(
                [
                    'tournament_registration_id' => $registration->id,
                    'player_id'                  => $player->id,
                ],
                [
                    'jersey_number' => $validated['jersey_number'] ?? null,
                    'is_active'     => true,
                ]
            );

            return response()->json([
                'message' => 'Jugador registrado e inscrito en la plantilla correctamente.',
                'player'  => array_merge($player->toArray(), [
                    'jersey_number' => $roster->jersey_number,
                    'is_active'     => $roster->is_active,
                ]),
            ], 201);
        });
    }
}