<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TournamentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Tournament::withCount('registrations')
            ->orderBy('created_at', 'desc');

        // 🛡️ Si el usuario NO es administrador (es delegado), solo puede ver el torneo activo
        if (!$user || !$user->hasRole('admin')) {
            $query->where('status', 'in_progress');
        }

        $tournaments = $query->paginate(10);

        return response()->json($tournaments);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'edition' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'entry_fee' => 'required|numeric|min:0',
            'referee_fee_per_match' => 'required|numeric|min:0',
            'status' => 'required|in:draft,in_progress,completed,cancelled',
        ]);

        $tournament = Tournament::create($validated);

        return response()->json([
            'message' => 'Torneo creado exitosamente.',
            'tournament' => $tournament
        ], 201);
        //return redirect()->route('admin.tournaments.index')
         //   ->with('success', 'Torneo creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tournament $tournament)
    {
        $tournament->load(['groups', 'registrations.team', 'rounds']);

        return response()->json($tournament);
        //return view('admin.tournaments.show', compact('tournament'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tournament $tournament)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tournament $tournament)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tournament $tournament)
    {
        //
    }

    // Método general para actualizar el estado del torneo
    public function updateStatus(Request $request, Tournament $tournament): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,in_progress,completed,cancelled',
        ]);

        // Si se está activando este torneo ('in_progress'), opcionalmente puedes pasar los demás a 'completed' o 'draft'
        if ($validated['status'] === 'in_progress') {
            Tournament::where('id', '!=', $tournament->id)
                    ->where('status', 'in_progress')
                    ->update(['status' => 'completed']);
        }

        $tournament->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Estado del torneo actualizado exitosamente.',
            'tournament' => $tournament
        ]);
    }
}
