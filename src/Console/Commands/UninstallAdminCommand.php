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
        $this->line('<info>Uninstalling laravel admin!</info>');
    }

    /**
     * Remove files and directories.
     *
     * @return void
     */
    protected function removeFilesAndDirectories()
    {
        $this->files->delete(config_path('admin.php'));
        $this->files->delete(app_path('routes/admin.php'));
        $this->files->deleteDirectory(app_path('Http/Controllers/Admin'));
        $this->files->deleteDirectory(resource_path('views/auth'));
        $this->files->deleteDirectory(public_path('templates'));
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
