<?php

namespace Guesl\Admin\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class UninstallAdminCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'guesl:uninstall';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uninstall the admin package';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->confirm('Are you sure to uninstall laravel-admin?')) {
            return;
        }
        $this->removeFilesAndDirectories();
        $this->info('Uninstalling laravel admin!');
    }

    /**
     * Remove files and directories.
     *
     * @return void
     */
    protected function removeFilesAndDirectories()
    {
        $this->files->delete($adminConfigPath = config_path('admin.php'));
        $this->info($adminConfigPath . ' deleted successfully.');

        $this->files->delete($adminRoutePath = app_path('routes/admin.php'));
        $this->info($adminRoutePath . ' deleted successfully.');

        $this->files->deleteDirectory($adminControllersPath = app_path('Http/Controllers/Admin'));
        $this->info($adminControllersPath . ' deleted successfully.');

        $this->files->deleteDirectory($authResource = resource_path('views/auth'));
        $this->info($authResource . ' deleted successfully.');

        $this->files->deleteDirectory($templatesPath = public_path('templates'));
        $this->info($templatesPath . ' deleted successfully.');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return '';
    }
}
