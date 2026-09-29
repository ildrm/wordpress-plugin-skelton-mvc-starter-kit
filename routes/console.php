<?php

declare(strict_types=1);

use StarterKit\Mvc\Console\Commands\StatusCommand;
use StarterKit\Mvc\Console\Commands\MigrateCommand;

return [
    'wordpress-mvc-starter status' => StatusCommand::class,
    'wordpress-mvc-starter migrate' => MigrateCommand::class,
];
