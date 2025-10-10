<?php

namespace App\Filament\Widgets\Concerns;

use Illuminate\Support\Facades\Auth;

trait HasRoleVisibility
{
    /**
     * Determine if the authenticated user can view the widget
     * based on the configured visible roles.
     */
    protected static function canViewWidgetByRole(): bool
    {
        $roles = static::getVisibleRoles();

        // Empty roles array means the widget is visible to everyone.
        if (empty($roles)) {
            return true;
        }

        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole($roles)) {
            return true;
        }

        if (property_exists($user, 'role')) {
            return in_array($user->role, $roles, true);
        }

        return false;
    }

    /**
     * Resolve which roles can see the widget.
     *
     * Widgets can override the $visibleRoles property or the method
     * to specialise the allowed roles.
     *
     * @return array<int, string>
     */
    protected static function getVisibleRoles(): array
    {
        $class = static::class;

        if (property_exists($class, 'visibleRoles') && is_array($class::$visibleRoles)) {
            return $class::$visibleRoles;
        }

        return ['admin', 'manager'];
    }
}
