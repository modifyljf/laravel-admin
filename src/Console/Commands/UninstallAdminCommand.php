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
    protected $signature = 'guesl:uninstall';

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
        $this->info("Deleted: $adminConfigPath.");

        $this->files->delete($adminRoutePath = base_path('routes/admin.php'));
        $this->info("Deleted: $adminRoutePath.");

        $this->files->deleteDirectory($contractPath = app_path('Contracts'));
        $this->info("Deleted: $contractPath.");

        $this->files->deleteDirectory($gueslImagePath = public_path('images/admin'));
        $this->info("Deleted: $gueslImagePath.");

        $this->files->deleteDirectory($adminControllersPath = app_path('Http/Controllers/Admin'));
        $this->info("Deleted: $adminControllersPath.");

        $this->files->deleteDirectory($authResourcePath = resource_path('views/auth'));
        $this->info("Deleted: $authResourcePath.");

        $this->files->deleteDirectory($adminResourcePath = resource_path('views/admin'));
        $this->info("Deleted: $adminResourcePath.");

        $this->files->deleteDirectory($adminAssetsPath = resource_path('assets/js'));
        $this->info("Deleted: $adminAssetsPath.");

        $this->files->deleteDirectory($templatesPath = public_path('templates'));
        $this->info("Deleted: $templatesPath.");

        $this->files->deleteDirectory($publicAdminAssetsPath = public_path('js/admin'));
        $this->info("Deleted: $publicAdminAssetsPath.");
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
