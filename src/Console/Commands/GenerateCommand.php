<?php

namespace Guesl\Admin\Console\Commands;

use Guesl\Admin\Contracts\Constant;
use Illuminate\Console\GeneratorCommand;

class GenerateCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guesl:generate {name}
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
        $name = $this->getNameInput();
        $this->makeDirectory(resource_path("views/admin/$name"));
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
        $this->call("guesl:controller $name");
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
        $this->call("make:model $modelName");
    }

    /**
     * Get the views that need to be exported.
     *
     * @return array
     */
    protected function getViews()
    {
        $template = $this->option('template') ?: Constant::TEMPLATE_DEFAULT;
        $name = $this->getNameInput();

        return [
            "templates/{$template}/module/index.blade.stub" => "$name/index.blade.php",
            "templates/{$template}/module/edit.blade.stub" => "$name/edit.blade.php",
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
            if (file_exists($view = resource_path('views/' . $value)) && !$this->option('force')) {
                if (!$this->confirm("The [{$value}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                __DIR__ . '/stubs/make/views/' . $key,
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
        $name = $this->getNameInput();

        return [
            "{$template}/assets/js/index.js.stub" => "js/$name/index.js",
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
                if (!$this->confirm("The [{$value}] file already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                __DIR__ . '/stubs/make/resources/' . $key,
                $asset
            );

            $this->info('Generated: ' . $asset);
        }
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
