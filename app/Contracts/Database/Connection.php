<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Contracts\Database;

interface Connection
{
    public function table(string $name): string;
    public function select(string $sql, array $values = []): array;
    public function insert(string $table, array $data): int;
    public function update(string $table, array $data, array $where): int;
    public function delete(string $table, array $where): int;
}
