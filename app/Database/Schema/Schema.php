<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Database\Schema;

use StarterKit\Mvc\Contracts\Database\Connection;

final class Schema
{
    public function __construct(private Connection $connection, private \wpdb $wpdb)
    {
    }

    public function create(string $table, callable $definition): void
    {
        $blueprint = new Blueprint();
        $definition($blueprint);
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $name = $this->connection->table($table);
        $sql = "CREATE TABLE {$name} (\n" . $blueprint->definitions() . "\n) " . $this->wpdb->get_charset_collate() . ';';
        dbDelta($sql);
        if (!$this->hasTable($table)) {
            throw new \RuntimeException("Failed to create table {$table}.");
        }
    }

    public function hasTable(string $table): bool
    {
        $name = $this->connection->table($table);
        $sql = $this->wpdb->prepare('SHOW TABLES LIKE %s', $this->wpdb->esc_like($name));
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Prepared immediately above.
        return $this->wpdb->get_var($sql) === $name;
    }

    public function drop(string $table): void
    {
        $name = $this->connection->table($table);
        $sql = $this->wpdb->prepare('DROP TABLE IF EXISTS %i', $name);
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Prepared identifier above.
        $this->wpdb->query($sql);
    }
}
