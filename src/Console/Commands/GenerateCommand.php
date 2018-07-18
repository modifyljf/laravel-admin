<?php

namespace Guesl\Admin\Console\Commands;

use Guesl\Admin\Contracts\Constant;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class GenerateCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guesl:generate 
                    {name : The name of model.}
                    {--template : Template name, "metronic" as default.}
                    {--force : Overwrite existing objects by default.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic admin views and routes';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->createDirectories();

        $this->makeController();
        $this->makeModel();
        $this->makeViews();
        $this->makeAssets();

        $this->info('Admin views generated successfully.');
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        $name = strtolower($this->getNameInput());
        $this->makeDirectory(resource_path("views/admin/modules/$name"));
        $this->makeDirectory(resource_path("assets/js/admin/$name"));
        $this->makeDirectory(public_path("js/admin/$name"));
    }

    /**
     * Make controllers.
     *
     * @return void
     */
    protected function makeController()
    {
        $name = $this->getNameInput();
        $controllerName = $this->getControllerName($name);

        $this->call('guesl:controller', [
            'name' => $controllerName
        ]);
    }

    /**
     * Get controller name.
     *
     * @param $name
     * @return string
     */
    protected function getControllerName($name)
    {
        return strpos($name, 'Controller') !== false ? $name : $name . 'Controller';
    }

    /**
     * Make Model.
     *
     * @return void
     */
    protected function makeModel()
    {
        $name = $this->getNameInput();
        $modelName = "Models/$name";
        $this->call('make:model', [
            'name' => $modelName
        ]);
    }

    /**
     * Get the views that need to be exported.
     *
     * @return array
     */
    protected function getViews()
    {
        $template = $this->option('template') ?: Constant::TEMPLATE_DEFAULT;
        $name = strtolower($this->getNameInput());

        return [
            "{$template}/module/index.blade.stub" => "$name/index.blade.php",
            "{$template}/module/edit.blade.stub" => "$name/edit.blade.php",
        ];
    }

    /**
     * Make views.
     *
     * @return void
     */
    protected function makeViews()
    {
        $views = $this->getViews();
        foreach ($views as $key => $value) {
            if (file_exists($view = resource_path('views/admin/modules/' . $value)) && !$this->option('force')) {
                if (!$this->confirm("The [{$value}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                __DIR__ . '/stubs/make/views/templates/' . $key,
                $view
            );

            $this->info('Generated: ' . $view);
        }
    }

    /**
     * Get the assets that need to be exported.
     *
     * @return array
     */
    protected function getAssets()
    {
        $template = $this->option('template') ?: Constant::TEMPLATE_DEFAULT;
        $name = strtolower($this->getNameInput());

        return [
            "{$template}/assets/js/index.js.stub" => "js/admin/$name/index.js",
        ];
    }

    /**
     * Make assets.
     *
     * @return void
     */
    protected function makeAssets()
    {
        $assets = $this->getAssets();
        foreach ($assets as $key => $value) {
            if (file_exists($asset = resource_path('assets/' . $value)) && !$this->option('force')) {
                if (!$this->confirm("The [{$value}] file already exists under resource path. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                __DIR__ . '/stubs/make/resources/' . $key,
                $asset
            );

            if (file_exists($assetPublic = public_path($value)) && !$this->option('force')) {
                if (!$this->confirm("The [{$value}] file already exists under public path. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                __DIR__ . '/stubs/make/resources/' . $key,
                $assetPublic
            );

            $this->info('Generated: ' . $asset);
        }
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
     * @return string
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
