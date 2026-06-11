<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    protected static bool $databaseReady = false;

    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (! static::$databaseReady) {
            $this->ensureTestingDatabaseExists();
            Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
            static::$databaseReady = true;
        }
    }

    private function ensureTestingDatabaseExists(): void
    {
        $connection = mysqli_connect(
            env('DB_HOST', '127.0.0.1'),
            env('DB_USERNAME', 'root'),
            env('DB_PASSWORD', ''),
            null,
            (int) env('DB_PORT', 3306),
        );

        if (! $connection) {
            throw new \RuntimeException('Gagal terhubung ke MySQL untuk menyiapkan database testing.');
        }

        $database = env('DB_DATABASE', 'dblms_test');
        mysqli_query($connection, sprintf('CREATE DATABASE IF NOT EXISTS `%s`', $database));
        mysqli_close($connection);
    }
}
