<?php

namespace Guesl\Admin\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class ControllerMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guesl:controller {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class for admin.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->exportBaseController();
        $this->makeController();

        $controllerName = $this->getNameInput();
        $this->info("BaseController exported and {$controllerName}Controller generated.");
    }

    /**
     * Export BaseController.
     *
     * @return void
     */
    protected function exportBaseController()
    {
        $baseControllerPath = app_path('Http/Controllers/Admin/BaseController.php');
        file_put_contents(
            $baseControllerPath,
            $this->compileBaseControllerStub()
        );

        $this->info($baseControllerPath . ' generated successfully.');
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
     * Get the admin controller namespace for the class.
     *
     * @return string
     */
    protected function adminControllerNamespace()
    {
        return $this->rootNamespace() . 'Http/Controllers/Admin';
    }

    /**
     * Make controllers.
     *
     * @return void
     */
    protected function makeController()
    {
        $name = $this->getNameInput();
        $name = 'Admin/' . $name;

        $controllerPath = app_path("Http/Controllers/Admin/$name.php");
        file_put_contents(
            $controllerPath,
            $this->compileControllerStub($name)
        );

        $this->info($controllerPath . ' generated successfully.');
    }

    /**
     * Compiles the HomeController stub.
     *
     * @return string
     */
    protected function compileControllerStub($name)
    {
        return str_replace(
            ['DummyNamespace', 'DummyRootNamespace', 'DummyClass'],
            [$this->adminControllerNamespace(), $this->rootNamespace(), $this->qualifyClass($name)],
            file_get_contents(__DIR__ . '/stubs/make/controllers/Controller.stub')
        );
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/make/controllers';
    }
}
