<?php

declare(strict_types=1);

namespace StarterKit\Mvc\WordPress\Cron;

use StarterKit\Mvc\Contracts\Jobs\Job;
use StarterKit\Mvc\Foundation\Application;

final class CronRegistrar
{
    public const HOOK = 'starterkit_mvc_run_job';
    private array $scheduled = [];

    public function __construct(private Application $app)
    {
    }

    public function register(): void
    {
        add_action(self::HOOK, function (string $class): void {
            if (!isset($this->scheduled[$class])) {
                return;
            }
            $this->runNow($class);
        });
    }

    public function allow(string $class): void
    {
        if (!is_subclass_of($class, Job::class)) {
            throw new \InvalidArgumentException('Cron jobs must implement Job.');
        }
        $this->scheduled[$class] ??= ['class' => $class, 'interval' => null];
    }

    public function recurring(string $class, string $interval): void
    {
        $this->allow($class);
        $this->scheduled[$class] = ['class' => $class, 'interval' => $interval];
    }

    public function once(string $class, int $timestamp): void
    {
        if (!isset($this->scheduled[$class])) {
            throw new \InvalidArgumentException('Register one-time jobs with allow() during provider registration.');
        }
        if (!wp_next_scheduled(self::HOOK, [$class])) {
            wp_schedule_single_event($timestamp, self::HOOK, [$class]);
        }
    }

    public function runNow(string $class): void
    {
        $this->allow($class);
        $this->app->make($class)->handle();
    }

    public function schedule(): void
    {
        foreach ($this->scheduled as $job) {
            if ($job['interval'] === null) {
                continue;
            }
            if (!wp_next_scheduled(self::HOOK, [$job['class']])) {
                wp_schedule_event(time() + MINUTE_IN_SECONDS, $job['interval'], self::HOOK, [$job['class']]);
            }
        }
    }

    public function unschedule(): void
    {
        foreach ($this->scheduled as $job) {
            wp_clear_scheduled_hook(self::HOOK, [$job['class']]);
        }
    }
}
