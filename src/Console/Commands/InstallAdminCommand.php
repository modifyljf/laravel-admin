<?php

namespace Guesl\Admin\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class InstallAdminCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'guesl:install';

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
        $this->exportRoutes();
        $this->call('guesl:auth');
    }

    /**
     * Export the routes file.
     *
     * @return void
     */
    public function exportRoutes()
    {
        if (!file_exists($routePath = base_path('routes/admin.php'))) {
            copy(
                $this->getStub() . '/make/routes.stub',
                $routePath
            );

            $this->info($routePath . ' generated successfully.');
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
