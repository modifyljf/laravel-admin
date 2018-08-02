<?php

namespace Guesl\Admin\Console\Commands;

use Guesl\Admin\Contracts\Constant;
use Illuminate\Console\GeneratorCommand as Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

abstract class GeneratorCommand extends Command
{
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
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of model.'],
        ];
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
     * Get module name.
     *
     * @return string
     */
    protected function getModuleName()
    {
        $moduleInput = $this->option('module');
        return $moduleInput ? ucfirst(trim($moduleInput)) : null;
    }

    /**
     * Get template name.
     *
     * @return string
     */
    protected function getTemplate()
    {
        return $this->option('template') ?: Constant::TEMPLATE_DEFAULT;
    }

    /**
     * Get module constant name.
     *
     * @return string|null
     */
    protected function moduleConstantName()
    {
        $moduleConstantName = null;

        $module = $this->getModuleName();

        if ($module) {
            $moduleName = Str::snake($module);
            $moduleConstantName = strtoupper("MODULE_$moduleName");
        }

        return $moduleConstantName;
    }

    /**
     * Get menu constant name.
     *
     * @return string
     */
    protected function menuConstantName()
    {
        $menuConstantName = null;

        $name = $this->getNameInput();
        $module = $this->getModuleName();

        $menuName = Str::snake($name);

        if ($module) {
            $moduleName = Str::snake($module);
            $menuConstantName = strtoupper("MENU_{$moduleName}_$menuName");
        } else {
            $menuConstantName = strtoupper("MENU_$menuName");
        }

        return $menuConstantName;
    }

    /**
     * Get the table of the model.
     *
     * @return string
     */
    protected function getTableName()
    {
        $table = Str::plural(Str::snake(class_basename($this->argument('name'))));

        return $table;
    }

    /**
     * Get the table id by 'name' argument.
     *
     * @return string
     */
    protected function tableId()
    {
        $name = $this->argument('name');
        $tableId = lcfirst($name) . 'Table';

        return $tableId;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/make';
    }
}
