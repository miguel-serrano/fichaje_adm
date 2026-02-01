<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Event;

/**
 * Provee contexto enriquecido del usuario actual
 */
final class UserContextProvider
{
    public function getContext(): ?array
    {
        $user = auth()->user();

        if ($user === null) {
            return [
                'authenticated' => false,
                'source' => $this->determineSource(),
            ];
        }

        return [
            'authenticated' => true,
            'id' => $user->id,
            'email' => $user->email ?? null,
            'name' => $user->name ?? null,
            'type' => $this->determineUserType($user),
            'roles' => $this->getRoles($user),
            'permissions' => $this->getPermissions($user),
            'session' => $this->getSessionInfo(),
            'impersonated_by' => $this->getImpersonator(),
        ];
    }

    private function determineSource(): string
    {
        if (app()->runningInConsole()) {
            return 'cli';
        }

        if (request()?->hasHeader('X-Api-Key')) {
            return 'api_key';
        }

        return 'anonymous';
    }

    private function determineUserType(mixed $user): string
    {
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return 'super_admin';
        }

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return 'admin';
        }

        if (method_exists($user, 'isManager') && $user->isManager()) {
            return 'manager';
        }

        return 'employee';
    }

    private function getRoles(mixed $user): array
    {
        if (method_exists($user, 'roles')) {
            return $user->roles->pluck('name')->toArray();
        }

        return [];
    }

    private function getPermissions(mixed $user): array
    {
        if (method_exists($user, 'getAllPermissions')) {
            return $user->getAllPermissions()->pluck('name')->take(20)->toArray();
        }

        return [];
    }

    private function getSessionInfo(): ?array
    {
        if (!session()->isStarted()) {
            return null;
        }

        return [
            'id' => session()->getId(),
            'started_at' => session('_started_at'),
        ];
    }

    private function getImpersonator(): ?int
    {

        return session('impersonator_id');
    }
}
