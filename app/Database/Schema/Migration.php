<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Database\Schema;

interface Migration
{
    public function up(Schema $schema): void;
    public function down(Schema $schema): void;
}
