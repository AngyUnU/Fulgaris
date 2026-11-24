<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MobileSyncController extends Controller
{
    /**
     * Inicio de sesión desde la aplicación móvil.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string'],
        ]);

        $user = User::with('role')->where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $deviceName = $request->device_name ?? 'Ionic-Angular-MobileApp';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Autenticación exitosa',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ? [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                    'label' => $user->role->label,
                ] : null,
            ],
        ]);
    }

    /**
     * Obtiene los datos del perfil autenticado.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('role');

        return response()->json([
            'status' => 'success',
            'user' => $user,
        ]);
    }

    /**
     * Lista de actividades asignadas al integrante autenticado (para la app móvil).
     */
    public function myActivities(Request $request): JsonResponse
    {
        $user = $request->user();

        $activities = Activity::with([
            'project:id,code,name,status,due_date',
            'assignees:id,name,email',
            'comments.user:id,name',
        ])
            ->whereHas('assignees', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->orderBy('due_date', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'count' => $activities->count(),
            'data' => $activities,
        ]);
    }

    /**
     * Endpoint para que un integrante actualice el estado de una actividad.
     */
    public function updateStatus(Request $request, Activity $activity): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pendiente,en_progreso,en_revision,completada,atrasada'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'evidence' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user = $request->user();

        // Verificar que el usuario tenga asignada esta actividad o sea administrador
        $isAssigned = $activity->assignees()->where('users.id', $user->id)->exists();
        if (! $isAssigned && ! $user->hasRole(['admin', 'lider'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'No tienes permisos para actualizar una actividad que no te ha sido asignada.',
            ], 403);
        }

        $activity->update([
            'status' => $validated['status'],
            'completed_at' => $validated['status'] === 'completada'
                ? ($activity->completed_at ?? now())
                : null,
        ]);

        if (! empty($validated['comment']) || $request->hasFile('evidence')) {
            ActivityComment::createWithEvidence([
                'activity_id' => $activity->id,
                'user_id' => $user->id,
                'comment' => $validated['comment'] ?? 'Evidencia adjunta.',
                'source' => 'mobile',
            ], $request->file('evidence'));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Estado actualizado.',
            'activity' => $activity->fresh(['project', 'assignees']),
        ]);
    }

    /**
     * Cerrar sesión y revocar el token móvil.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Sesión cerrada exitosamente en el dispositivo móvil.',
        ]);
    }
}
