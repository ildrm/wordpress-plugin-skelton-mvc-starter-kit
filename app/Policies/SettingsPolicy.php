<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Policies;

final class SettingsPolicy
{
    public function update(\WP_User $user): bool
    {
        return user_can($user, 'manage_options');
    }
}
