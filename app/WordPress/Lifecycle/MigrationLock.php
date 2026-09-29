<?php

declare(strict_types=1);

namespace StarterKit\Mvc\WordPress\Lifecycle;

final class MigrationLock
{
    public const OPTION = 'starterkit_mvc_upgrade_lock';

    public function __construct(private \wpdb $wpdb)
    {
    }

    public function acquire(): ?string
    {
        $token = time() . ':' . bin2hex(random_bytes(16));
        if (add_option(self::OPTION, $token, '', false)) {
            return $token;
        }
        $current = get_option(self::OPTION, '');
        if (!is_string($current) || (int) strtok($current, ':') > time() - 300) {
            return null;
        }
        $sql = $this->wpdb->prepare(
            'UPDATE %i SET option_value = %s WHERE option_name = %s AND option_value = %s',
            $this->wpdb->options,
            $token,
            self::OPTION,
            $current
        );
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Query and table identifier were prepared above.
        if ($this->wpdb->query($sql) !== 1) {
            return null;
        }
        wp_cache_delete(self::OPTION, 'options');
        return $token;
    }

    public function release(string $token): void
    {
        $sql = $this->wpdb->prepare(
            'DELETE FROM %i WHERE option_name = %s AND option_value = %s',
            $this->wpdb->options,
            self::OPTION,
            $token
        );
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- The token and identifier were prepared above.
        $this->wpdb->query($sql);
        wp_cache_delete(self::OPTION, 'options');
    }
}
