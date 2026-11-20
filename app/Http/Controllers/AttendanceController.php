<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'user_license' => 'required|max:255',
            'id_licence' => 'required|string', 
            'device_token' => 'nullable|string|max:255', 
        ]);

        if ($validated['user_license'] === '1' && $validated['id_licence'] === '1' && $validated['device_token'] === '1') {
            return response()->json([
            'status'  => 'ok',
            'mode'    => 'test_valid',
            'message' => 'Asistencia de prueba registrada como válida.',
            'data'    => [
                'user_license' => $validated['user_license'],
                'id_licence'   => $validated['id_licence'],
                'device_token' => $validated['device_token'],
                'marked_at'    => now()->toIso8601String(),
            ],
        ], 201);
        }
        if ($validated['user_license'] !== '1' && $validated['id_licence'] === '1' && $validated['device_token'] === '1') {
            return response()->json([
            'status'  => 'ok',
            'mode'    => 'test_valid',
            'message' => 'Numero de carnet no valido.',
            'data'    => [
                'user_license' => $validated['user_license'],
                'id_licence'   => $validated['id_licence'],
                'device_token' => $validated['device_token'],
                'marked_at'    => now()->toIso8601String(),
            ],
        ], 201);
        }
        if ($validated['user_license'] === '1' && $validated['id_licence'] !== '1' && $validated['device_token'] === '1') {
            return response()->json([
            'status'  => 'ok',
            'mode'    => 'test_valid',
            'message' => 'Carnet no valido',
            'data'    => [
                'user_license' => $validated['user_license'],
                'id_licence'   => $validated['id_licence'],
                'device_token' => $validated['device_token'],
                'marked_at'    => now()->toIso8601String(),
            ],
        ], 201);
        }
        if ($validated['user_license'] === '1' && $validated['id_licence'] === '1' && $validated['device_token'] !== '1') {
            return response()->json([
            'status'  => 'ok',
            'mode'    => 'test_valid',
            'message' => 'Dispositivo no valido',
            'data'    => [
                'user_license' => $validated['user_license'],
                'id_licence'   => $validated['id_licence'],
                'device_token' => $validated['device_token'],
                'marked_at'    => now()->toIso8601String(),
            ],
        ], 201);
        }

        return response()->json([
        'status'  => 'ok',
        'mode'    => 'standard_test',
        'message' => 'Datos validados; listo para conectar consulta a BD y verificación real.',
        'data'    => [
            'user_license' => $validated['user_license'],
            'id_licence'   => $validated['id_licence'],
            'device_token' => $validated['device_token'],
            'marked_at'    => now()->toIso8601String(),
        ],
    ], 200);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $tournamentRoster)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $tournamentRoster)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $tournamentRoster)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $tournamentRoster)
    {
        //
    }
}
