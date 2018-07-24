<?php

namespace Guesl\Admin\Console\Commands;

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
        $this->makeModuleConstant();
        $this->makeController();

        $controllerName = $this->getNameInput();

        $this->info('Successful: ' . "BaseController exported and {$controllerName}Controller generated.");
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
                'DummyModeClass',
                'DummyLowerModel',
                'DummyMenuConstant',
                'DummyModuleConstant',
            ],
            [
                $this->adminControllerNamespace(),
                $this->rootNamespace(),
                $this->controllerName($name),
                $this->modelNamespace(),
                $this->modelName($name),
                strtolower($name),
                $this->menuConstantName(),
                $this->moduleConstantName() ? $this->moduleConstantName() : '',
            ],
            file_get_contents(__DIR__ . '/stubs/make/controllers/Controller.stub')
        );
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
}
