<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Database;

use StarterKit\Mvc\Contracts\Database\Connection as ConnectionContract;

final class Connection implements ConnectionContract
{
    public function __construct(private \wpdb $wpdb)
    {
    }

    public function table(string $name): string
    {
        if (!preg_match('/^[a-z][a-z0-9_]*$/D', $name)) {
            throw new \InvalidArgumentException('Invalid table identifier.');
        }
        return $this->wpdb->prefix . $name;
    }

    public function select(string $sql, array $values = []): array
    {
        if ($values === []) {
            throw new \InvalidArgumentException('A prepared select requires at least one value.');
        }
        $sql = $this->wpdb->prepare($sql, ...$values); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- This wrapper accepts SQL templates and binds every value.
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql was prepared above.
        return $this->wpdb->get_results($sql, ARRAY_A) ?: [];
    }

    public function insert(string $table, array $data): int
    {
        $result = $this->wpdb->insert($this->table($table), $data);
        if ($result === false) {
            throw new \RuntimeException('Database insert failed.');
        }
        return (int) $this->wpdb->insert_id;
    }

    public function update(string $table, array $data, array $where): int
    {
        $result = $this->wpdb->update($this->table($table), $data, $where);
        if ($result === false) {
            throw new \RuntimeException('Database update failed.');
        }
        return $result;
    }

    public function delete(string $table, array $where): int
    {
        $result = $this->wpdb->delete($this->table($table), $where);
        if ($result === false) {
            throw new \RuntimeException('Database delete failed.');
        }
        return $result;
    }
}
