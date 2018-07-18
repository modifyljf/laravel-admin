<?php

namespace Guesl\Admin\Console\Commands;

use Guesl\Admin\Contracts\Constant;
use Illuminate\Console\GeneratorCommand;

class InstallAdminCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'guesl:install
                    {--force : Overwrite existing views by default}
                    {--template : Template name, "metronic" as default.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the admin package';

    /**
     * Install directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->initDatabase();
        $this->exportConfig();
        $this->exportRoutes();
        $this->call('guesl:auth', [
            '--template' => $this->option('template') ?: Constant::TEMPLATE_DEFAULT,
            '--force' => $this->option('force')
        ]);

        $this->call('guesl:admin', [
            '--template' => $this->option('template') ?: Constant::TEMPLATE_DEFAULT,
            '--force' => $this->option('force')
        ]);
    }

    /**
     * Export the routes file.
     *
     * @return void
     */
    public function exportConfig()
    {
        if (!file_exists($configPath = config_path('admin.php')) || $this->option('force')) {
            copy(
                __DIR__ . '/../../../config/admin.php',
                $configPath
            );

            $this->info('Generated: ' . $configPath);
        }
    }

    /**
     * Export the routes file.
     *
     * @return void
     */
    public function exportRoutes()
    {
        if (!file_exists($routePath = base_path('routes/admin.php')) || $this->option('force')) {
            copy(
                __DIR__ . '/../../../routes/admin.php',
                $routePath
            );

            $this->info('Generated: ' . $routePath);
        }
    }

    /**
     * Create tables and seed it.
     *
     * @return void
     */
    public function initDatabase()
    {
        $this->call('migrate');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs';
    }
}
