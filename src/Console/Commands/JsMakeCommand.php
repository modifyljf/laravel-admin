<?php

namespace Guesl\Admin\Console\Commands;

use Guesl\Admin\Contracts\Constant;

class JsMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guesl:js 
                    {name : The name of model.}
                    {--template : Template name, "metronic" as default.}
                    {--force : Overwrite existing objects by default.}
                    {--module= : The module which the `name` belongs to.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic admin js, index js file.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->createDirectories();

        $this->makePackageJson();
        $this->exportComponents();
        $this->makeAssets();
        $this->exportWebpackIndexJs();

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
        $this->makeDirectory(resource_path("assets/admin/js/$name"));
        $this->makeDirectory(public_path("admin/js/$name"));
        $this->makeDirectory(resource_path('assets/admin/js/components'));
    }

    /**
     * Make or Update package json file.
     */
    protected function makePackageJson()
    {
        $packageJsonPath = base_path('package.json');
        if (file_exists($packageJsonPath) && !$this->option('force')) {
            if (!$this->confirm("The [{$packageJsonPath}] library config file already exists. Do you want to replace it?")) {
                return;
            }
        }

        file_put_contents(
            $packageJsonPath,
            file_get_contents(__DIR__ . '/../../../package.json')
        );

        $this->info('Created: ' . $packageJsonPath);
    }

    /**
     * Export common components.
     */
    protected function exportComponents()
    {
        $dataTableComponent = resource_path('assets/admin/js/components/DataTableComponent.js');

        if (file_exists($dataTableComponent)) {
            $this->error('DataTableComponent already exists.');
        }

        $template = $this->getTemplate();

        file_put_contents(
            $dataTableComponent,
            file_get_contents(__DIR__ . "/stubs/make/resources/${template}/assets/components/DataTableComponent.js")
        );
    }

    /**
     * Make assets.
     */
    protected function makeAssets()
    {
        $this->makeIndexAssets();

        $this->info('Successful: Assets Generated.');
    }

    /**
     * Make index assets.
     */
    protected function makeIndexAssets()
    {
        $this->makeResourceIndexJs();
    }

    /**
     * Make js file under resource folder.
     */
    protected function makeResourceIndexJs()
    {
        $name = strtolower($this->getNameInput());
        $assetModel = strtolower($name);

        $indexJsPath = resource_path('assets/admin/js/' . $assetModel . '/index.js');
        if (file_exists($indexJsPath) && !$this->option('force')) {
            if (!$this->confirm("The [{$indexJsPath}] view already exists. Do you want to replace it?")) {
                return;
            }
        }

        file_put_contents(
            $indexJsPath,
            $this->compileIndexJsStub()
        );

        $this->info('Created: ' . $indexJsPath);
    }

    /**
     * Compile the index js stub.
     *
     * @return string
     */
    protected function compileIndexJsStub()
    {
        $stub = $this->getIndexJsStub();

        return str_replace(
            [
                'DummyTableId',
                'DummyResource',
            ],
            [
                $this->tableId(),
                str_plural(strtolower($this->getNameInput())),
            ],
            file_get_contents($stub)
        );
    }

    /**
     * Get the template view stub.
     *
     * @return string
     */
    protected function getIndexJsStub()
    {
        $template = $this->getTemplate();

        return __DIR__ . "/stubs/make/resources/${template}/assets/js/index.js.stub";
    }

    /**
     * Export index js webpack configuration to webpack.mix.js file.
     *
     * @return string
     */
    protected function exportWebpackIndexJs()
    {
        $webpackPath = base_path('webpack.mix.js');

        if (file_exists($webpackPath)) {
            file_put_contents(
                $webpackPath,
                $this->compileIndexJsConfig(),
                FILE_APPEND
            );

            $this->info('Updated: Add index js to webpack.mix.js.');
        } else {
            $this->error('Webpack file does not exists.');
        }
    }

    /**
     * Compile index js webpack configuration.
     *
     * @return string
     */
    protected function compileIndexJsConfig()
    {
        $template = $this->getTemplate();
        $modelName = strtolower($this->getNameInput());

        return str_replace(
            ['DummyModel'],
            [$modelName],
            file_get_contents(__DIR__ . "/stubs/make/resources/${template}/assets/js/webpack.mix.js")
        );
    }
}
