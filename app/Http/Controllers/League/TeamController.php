<?php
namespace App\Http\Controllers\League;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TournamentRegistration;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    /**
     * Listar todos los equipos registrados con búsqueda por nombre.
     */
    public function index(Request $request): JsonResponse
{
    $user = $request->user();

    $query = Team::with(['delegate:id,name,last_name,email,phone,dni', 'players'])
                 ->withCount('registrations');

    // 🛡️ Si el usuario NO es administrador, filtramos solo sus propios equipos (Delegado)
    if (!$user->hasRole('admin')) {
        $query->where('delegate_id', $user->id);
    }

    // Búsqueda opcional por nombre del equipo
    if ($request->has('search') && !empty($request->search)) {
        $query->where('name', 'like', "%{$request->search}%");
    }

    $teams = $query->orderBy('name', 'asc')->paginate(15);

    return response()->json($teams);
}

    /**
     * Crear un nuevo equipo en el sistema.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'delegate_cedula' => 'required|string', // Ya no exigimos 'exists' porque lo manejaremos nosotros
            'delegate_name' => 'nullable|string|max:255', // Opcional: nombre por si hay que crearlo
            'primary_color' => 'nullable|string|size:7',
            'secondary_color' => 'nullable|string|size:7',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 1. Buscamos al usuario por su cédula
        $delegate = User::where('dni', $request->delegate_cedula)->first();

        // 2. Si NO existe, lo creamos automáticamente al vuelo
        if (!$delegate) {
            $delegate = User::create([
                'name' => $request->delegate_name ?? 'Delegado',
                'last_name' => $request->delegate_last_name ?? 'Temporal', // 👈 ¡Obligatorio por tu base de datos!
                'dni' => $request->delegate_cedula,
                'email' => 'delegado_' . $request->delegate_cedula . '@futsal.local',
                'password' => Hash::make('12345678'),
                'role_id' => 3, // 👈 Ajusta este número según el ID del rol "Delegado" en tu BD (visto en tu error)
            ]);
            $delegate->assignRole('delegate');
        }

        // 3. Procesamos el logo si se subió
        $logoPath = null;
        if ($request->hasFile('logo_path')) {
            $logoPath = $request->file('logo_path')->store('teams', 'public');
        }

        // 4. Creamos el equipo usando el ID del delegado (sea que ya existía o se acaba de crear)
        $team = Team::create([
            'name' => $request->name,
            'delegate_id' => $delegate->id,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'logo_path' => $logoPath,
        ]);

        return response()->json([
            'message' => '¡Equipo y delegado procesados con éxito!',
            'team' => $team
        ], 201);
    }

    /**
     * Ver el detalle de un equipo (su delegado, sus inscripciones en torneos y plantilla activa).
     */
    public function show(Team $team): JsonResponse
    {
        $team->load([
            'delegate:id,name,last_name,email,phone',
            'registrations.tournament',
            'registrations.group',
            'registrations.roster.player'
        ]);

        return response()->json($team);
    }

    /**
     * Actualizar la información del equipo.
     */
    public function update(Request $request, Team $team): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'delegate_cedula' => 'required|string',
            'delegate_name' => 'nullable|string|max:255',
            'delegate_last_name' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|size:7',
            'secondary_color' => 'nullable|string|size:7',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 1. Verificar o gestionar al delegado por si cambió la cédula
        $delegate = \App\Models\User::where('dni', $request->delegate_cedula)->first();

        if (!$delegate) {
            $delegate = \App\Models\User::create([
                'name' => $request->delegate_name ?? 'Delegado',
                'last_name' => $request->delegate_last_name ?? 'Temporal',
                'dni' => $request->delegate_cedula,
                'email' => 'delegado_' . $request->delegate_cedula . '@futsal.local',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role_id' => 3,
            ]);
        }

        // 2. Manejar el reemplazo del logo si subió uno nuevo
        $logoPath = $team->logo_path; // Mantiene el actual por defecto
        if ($request->hasFile('logo_path')) {
            // Opcional: borrar el logo anterior del storage si deseas limpiar espacio
            if ($team->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($team->logo_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($team->logo_path);
            }
            $logoPath = $request->file('logo_path')->store('teams', 'public');
        }

        // 3. Actualizar el equipo
        $team->update([
            'name' => $request->name,
            'delegate_id' => $delegate->id,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'logo_path' => $logoPath,
        ]);

        return response()->json([
            'message' => '¡Equipo actualizado con éxito!',
            'team' => $team->load('delegate') // Cargamos la relación si la necesitas
        ]);
    }

    /**
     * Eliminar un equipo del sistema.
     */
    public function destroy(Team $team): JsonResponse
    {
        // Borrar el archivo del logo si existe en el disco
        if ($team->logo_path && Storage::disk('public')->exists($team->logo_path)) {
            Storage::disk('public')->delete($team->logo_path);
        }

        $team->delete();

        return response()->json([
            'message' => 'Equipo eliminado correctamente del sistema.'
        ]);
    }


    // En tu controlador (ej. TournamentRegistrationController o TeamController)
    public function getTeamRoster(Request $request, int $teamId)
    {
        $tournamentId = $request->query('tournament_id');

        // Buscamos la inscripción del equipo en este torneo específico
        $registration = TournamentRegistration::where('team_id', $teamId)
            ->where('tournament_id', $tournamentId)
            ->with('players')
            ->first();

        if (!$registration) {
            return response()->json(['players' => []]);
        }

        return response()->json($registration);
    }
}