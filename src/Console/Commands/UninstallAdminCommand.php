<?php

namespace Guesl\Admin\Console\Commands;

use Illuminate\Console\Command;

class UninstallAdminCommand extends Command
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
        $this->laravel['files']->delete(config_path('admin.php'));
        $this->laravel['files']->delete(app_path('routes/admin.php'));
        $this->laravel['files']->deleteDirectory(app_path('Http/Controllers/Admin'));
        $this->laravel['files']->deleteDirectory(resource_path('views/auth'));
        $this->laravel['files']->deleteDirectory(public_path('templates'));
    }
}
