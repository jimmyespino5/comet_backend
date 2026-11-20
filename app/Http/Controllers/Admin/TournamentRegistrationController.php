<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TournamentRegistration;
//use App\Models\Tournament;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class TournamentRegistrationController extends Controller
{
    /**
     * Listar las inscripciones de un torneo específico (con sus equipos y grupos)
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
        ]);

        $registrations = TournamentRegistration::with(['team.delegate', 'group', 'roster.player'])
            ->where('tournament_id', $request->tournament_id)
            ->get();

        return response()->json($registrations);
    }

    /**
     * Inscribir un equipo existente a un torneo
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            'team_id'       => 'required|exists:teams,id',
            'group_id'      => 'nullable|exists:groups,id',
        ]);

        // Verificar permisos si el usuario es Delegado
        $user = $request->user();
        if ($user->role->name === 'delegate') {
            $team = Team::findOrFail($validated['team_id']);
            if ($team->delegate_id !== $user->id) {
                return response()->json([
                    'message' => 'No tienes permisos para inscribir un equipo que no te pertenece.'
                ], 403);
            }
        }

        // Evitar duplicar la inscripción en el mismo torneo
        $existing = TournamentRegistration::where('tournament_id', $validated['tournament_id'])
            ->where('team_id', $validated['team_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Este equipo ya está inscrito en el torneo seleccionado.'
            ], 422);
        }

        $registration = TournamentRegistration::create([
            'tournament_id'  => $validated['tournament_id'],
            'team_id'        => $validated['team_id'],
            //'group_id'       => $validated['group_id'] ?? null,
            'payment_status' => 'pending',
        ]);

        return response()->json([
            'message'      => 'Equipo inscrito exitosamente en el torneo.',
            'registration' => $registration->load(['tournament', 'team', 'group'])
        ], 201);
    }

    /**
     * Asignar o cambiar de grupo a un equipo inscrito
     */
    public function updateGroup(Request $request, TournamentRegistration $registration): JsonResponse
    {
        $validated = $request->validate([
            'group_id' => 'nullable|exists:groups,id',
        ]);

        $registration->update(['group_id' => $validated['group_id']]);

        return response()->json([
            'message'      => 'Grupo actualizado correctamente.',
            'registration' => $registration->load('group')
        ]);
    }

    /**
     * Cancelar la inscripción de un equipo en un torneo
     */
    public function destroy(TournamentRegistration $registration): JsonResponse
    {
        $registration->delete();

        return response()->json([
            'message' => 'Inscripción del equipo eliminada del torneo.'
        ]);
    }
}