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
    protected $signature = 'guesl:controller 
                    {name}
                    {--module : The module which the `name` belongs to.}';

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
        $this->exportModuleConstant();
        $this->makeModuleConstant();
        $this->makeController();

        $controllerName = $this->getNameInput();

        $this->info('Successful: ' . "BaseController exported and {$controllerName}Controller generated.");
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
     * Export Module Constant file.
     *
     * @return void
     */
    protected function exportModuleConstant()
    {
        $this->makeDirectory(app_path('Contracts'));

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
     * Make module constants.
     *
     * @return void
     */
    protected function makeModuleConstant()
    {
        $moduleConstantPath = app_path('Contracts/ModuleConstant.php');

        file_put_contents(
            $moduleConstantPath,
            $this->getNewModuleConstantFileContent()
        );
    }

    /**
     * Get new module constant file content.
     *
     * @return string
     */
    protected function getNewModuleConstantFileContent()
    {
        $moduleConstantPath = app_path('Contracts/ModuleConstant.php');

        $fileArray = file($moduleConstantPath);
        $module = $this->option('module');

        if ($module) {
            $moduleLine = $this->moduleConstantLine($fileArray);
            $menuLine = $this->menuConstantLine($fileArray);

        } else {
            $moduleLine = $this->moduleConstantLine($fileArray);
            $menuLine = null;
        }

        if ($moduleLine) {
            array_splice($fileArray, sizeof($fileArray) - 1, 0, $moduleLine);
        }

        if ($menuLine) {
            array_splice($fileArray, sizeof($fileArray) - 1, 0, $menuLine);
        }

        return implode("", $fileArray);
    }

    /**
     * Get the module constant line.
     *
     * @param  array $fileArray
     * @return string
     */
    protected function moduleConstantLine($fileArray)
    {
        $moduleConstant = $this->moduleConstantName();
        $moduleConstantLine = "    const $moduleConstant = '$moduleConstant';\n";

        $moduleExists = false;

        foreach ($fileArray as $line) {
            if (strpos($line, $moduleConstant) !== false) {
                $moduleExists = true;
                break;
            }
        }

        return !$moduleExists ? $moduleConstantLine : null;
    }

    /**
     * Get the menu constant line.
     *
     * @param  array $fileArray
     * @return string|null
     */
    protected function menuConstantLine($fileArray)
    {
        $menuConstant = $this->menuConstantName();

        if (!$menuConstant) {
            return null;
        }

        $menuConstantLine = "    const $menuConstant = '$menuConstant';\n";

        $menuExists = false;

        foreach ($fileArray as $line) {
            if (strpos($line, $menuConstant) !== false) {
                $menuExists = true;
                break;
            }
        }

        return !$menuExists ? $menuConstantLine : null;
    }

    /**
     * Make controllers.
     *
     * @return void
     */
    protected function makeController()
    {
        $name = $this->getNameInput();
        $controllerName = $this->controllerName($name);

        $controllerPath = app_path("Http/Controllers/Admin/$controllerName.php");

        if (file_exists($controllerPath)) {
            $this->error("Http/Controllers/Admin/$name.php already exists.");
            return;
        }

        file_put_contents(
            $controllerPath,
            $this->compileControllerStub()
        );

        $this->info('Generated: ' . $controllerPath);
    }

    /**
     * Compiles the HomeController stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        $name = $this->argument('name');

        return str_replace(
            [
                'DummyNamespace',
                'DummyRootNamespace',
                'DummyClass',
                'DummyModelNamespace',
                'DummyModel',
                'DummyLowerModel',
                'DummyModuleConstant',
                'DummyMenuConstant',
            ],
            [
                $this->adminControllerNamespace(),
                $this->rootNamespace(),
                $this->controllerName($name),
                $this->modelNamespace(),
                $this->modelName($name),
                strtolower($name),
                "ModuleConstant::{$this->moduleConstantName()}",
                $this->menuConstantName() ? "ModuleConstant::{$this->menuConstantName()}" : 'null',
            ],
            file_get_contents(__DIR__ . '/stubs/make/controllers/Controller.stub')
        );
    }

    /**
     * Get module constant name.
     *
     * @return string
     */
    protected function moduleConstantName()
    {
        $name = $this->argument('name');
        $module = $this->option('module');

        if ($module) {
            $moduleConstantName = strtoupper("MODULE_$module");
        } else {
            $moduleConstantName = strtoupper("MODULE_$name");
        }

        return $moduleConstantName;
    }

    /**
     * Get menu constant name.
     *
     * @return string|null
     */
    protected function menuConstantName()
    {
        $menuConstantName = null;

        $name = $this->argument('name');
        $module = $this->option('module');

        if ($module) {
            $menuConstantName = strtoupper("MENU_{$module}_$name");
        }

        return $menuConstantName;
    }

    /**
     * Get controller name.
     *
     * @param $name
     * @return string
     */
    protected function controllerName($name)
    {
        return strpos($name, 'Controller') !== false ? $name : $name . 'Controller';
    }

    /**
     * Get model name.
     *
     * @param $name
     * @return string
     */
    protected function modelName($name)
    {
        return ucfirst($name);
    }

    /**
     * Get model namespace.
     *
     * @return string
     */
    protected function modelNamespace()
    {
        return $this->getNamespace($this->rootNamespace()) . '\Models';
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
        return __DIR__ . '/stubs/make/controllers';
    }
}
