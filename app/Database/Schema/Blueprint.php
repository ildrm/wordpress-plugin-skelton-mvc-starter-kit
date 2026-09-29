<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Database\Schema;

final class Blueprint
{
    private array $columns = [];
    private array $keys = [];

    public function id(string $name = 'id'): self
    {
        return $this->column($name, 'bigint(20) unsigned NOT NULL AUTO_INCREMENT')->primary($name);
    }
    public function bigint(string $name): self
    {
        return $this->column($name, 'bigint(20) unsigned NOT NULL');
    }
    public function integer(string $name): self
    {
        return $this->column($name, 'int(11) NOT NULL');
    }
    public function string(string $name, int $length = 191): self
    {
        if ($length < 1 || $length > 191) {
            throw new \InvalidArgumentException('String length must be between 1 and 191.');
        }
        return $this->column($name, "varchar({$length}) NOT NULL");
    }
    public function text(string $name): self
    {
        return $this->column($name, 'text NOT NULL');
    }
    public function longText(string $name): self
    {
        return $this->column($name, 'longtext NOT NULL');
    }
    public function boolean(string $name): self
    {
        return $this->column($name, 'tinyint(1) NOT NULL DEFAULT 0');
    }
    public function datetime(string $name): self
    {
        return $this->column($name, 'datetime NOT NULL');
    }
    public function nullable(): self
    {
        $name = array_key_last($this->columns);
        if ($name === null) {
            throw new \LogicException('No column to make nullable.');
        }
        $this->columns[$name] = str_replace('NOT NULL', 'NULL', $this->columns[$name]);
        return $this;
    }
    public function primary(string $name): self
    {
        $this->keys[] = 'PRIMARY KEY  (' . $this->identifier($name) . ')';
        return $this;
    }
    public function index(string $name): self
    {
        $this->keys[] = 'KEY ' . $this->identifier($name) . ' (' . $this->identifier($name) . ')';
        return $this;
    }
    public function unique(string $name): self
    {
        $this->keys[] = 'UNIQUE KEY ' . $this->identifier($name) . ' (' . $this->identifier($name) . ')';
        return $this;
    }

    public function definitions(): string
    {
        $columns = [];
        foreach ($this->columns as $name => $definition) {
            $columns[] = $this->identifier($name) . ' ' . $definition;
        }
        return implode(",\n", [...$columns, ...$this->keys]);
    }

    private function column(string $name, string $definition): self
    {
        $this->columns[$this->identifier($name)] = $definition;
        return $this;
    }

    private function identifier(string $name): string
    {
        if (!preg_match('/^[a-z][a-z0-9_]*$/D', $name)) {
            throw new \InvalidArgumentException('Invalid SQL identifier.');
        }
        return $name;
    }
}
