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
     */
    public function handle()
    {
        $this->createDirectories();
        $this->initDatabase();
        $this->exportConfig();
        $this->exportRoutes();

        $this->exportBaseController();
        $this->exportModuleConstant();

        $this->exportHomeController();

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
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        $this->makeDirectory(app_path('Http/Controllers/Admin'));
        $this->makeDirectory(app_path('Contracts'));
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
     * Export BaseController.
     *
     * @return void
     */
    protected function exportBaseController()
    {
        $baseControllerPath = app_path('Http/Controllers/Admin/BaseController.php');

        if (!file_exists($baseControllerPath)) {
            file_put_contents(
                $baseControllerPath,
                $this->compileBaseControllerStub()
            );

            $this->info('Generated: ' . $baseControllerPath);
        }
    }

    /**
     * Compiles the BaseController stub.
     *
     * @return string
     */
    protected function compileBaseControllerStub()
    {
        return str_replace(
            ['DummyNamespace', 'DummyRootNamespace'],
            [$this->adminControllerNamespace(), $this->rootNamespace()],
            file_get_contents(__DIR__ . '/stubs/make/controllers/BaseController.stub')
        );
    }

    /**
     * Get the default namespace for the class.
     *
     * @return string
     */
    protected function adminControllerNamespace()
    {
        return $this->getNamespace($this->rootNamespace()) . '\Http\Controllers\Admin';
    }

    /**
     * Export Module Constant file.
     *
     * @return void
     */
    protected function exportModuleConstant()
    {
        $moduleConstantPath = app_path('Contracts/ModuleConstant.php');

        if (!file_exists($moduleConstantPath)) {
            file_put_contents(
                $moduleConstantPath,
                $this->compileModuleConstantStub()
            );

            $this->info('Generated: ' . $moduleConstantPath);
        }
    }

    /**
     * Compiles the BaseController stub.
     *
     * @return string
     */
    protected function compileModuleConstantStub()
    {
        return str_replace(
            ['DummyRootNamespace'],
            [$this->rootNamespace()],
            file_get_contents(__DIR__ . '/stubs/make/contracts/ModuleConstant.stub')
        );
    }

    /**
     * Export the HomeController.
     */
    protected function exportHomeController()
    {
        $homeControllerPath = app_path('Http/Controllers/Admin/HomeController.php');

        if (!file_exists($homeControllerPath)) {
            file_put_contents(
                $homeControllerPath,
                $this->compileHomeControllerStub()
            );

            $this->info('Generated: ' . $homeControllerPath);
        }
    }

    /**
     * Compiles the HomeController stub.
     *
     * @return string
     */
    protected function compileHomeControllerStub()
    {
        return str_replace(
            ['DummyNamespace', 'DummyRootNamespace'],
            [$this->controllerNamespace(), $this->rootNamespace()],
            file_get_contents($this->getHomeControllerStub())
        );
    }

    /**
     * Get the controller stub file for the generator.
     *
     * @return string
     */
    protected function getHomeControllerStub()
    {
        return __DIR__ . '/stubs/make/controllers/HomeController.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @return string
     */
    protected function controllerNamespace()
    {
        return $this->getNamespace($this->rootNamespace()) . '\Http\Controllers\Admin';
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
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     */
    protected function makeDirectory($path)
    {
        if (!is_dir($directory = $path)) {
            $this->files->makeDirectory($directory, 0755, true, true);
        }
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
