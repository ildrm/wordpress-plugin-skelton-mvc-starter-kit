<?php

return ['name' => 'WordPress MVC Starter', 'environment' => function_exists('wp_get_environment_type') ? wp_get_environment_type() : 'production'];
