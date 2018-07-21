<?php

namespace Guesl\Admin\Console\Commands;

use Illuminate\Console\GeneratorCommand as Command;
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
     * Get module constant name.
     *
     * @return string|null
     */
    protected function moduleConstantName()
    {
        $moduleConstantName = null;

        $module = $this->getModuleName();

        if ($module) {
            $moduleConstantName = strtoupper("MODULE_$module");
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

        if ($module) {
            $menuConstantName = strtoupper("MENU_{$module}_$name");
        } else {
            $menuConstantName = strtoupper("MENU_$name");
        }

        return $menuConstantName;
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
