<?php

namespace Guesl\Admin\Console\Commands;

class GenerateCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guesl:make
                    {name : The name of model.}
                    {--template : Template name, "metronic" as default.}
                    {--force : Overwrite existing objects by default.}
                    {--module= : The module which the `name` belongs to.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic admin controller, constant, views, routes and assets.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->createDirectories();

        $this->makeController();
        $this->makeRoute();
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
        $this->makeDirectory(resource_path("views/admin/models/$name"));
    }

    /**
     * Make controllers.
     *
     * @return void
     */
    protected function makeController()
    {
        $name = $this->getNameInput();
        $module = $this->getModuleName();

        $this->call('guesl:controller', [
            'name' => $name,
            '--module' => $module,
        ]);
    }

    /**
     * Make route.
     *
     * @return void
     */
    protected function makeRoute()
    {
        $adminRoute = base_path('routes/admin.php');

        file_put_contents(
            $adminRoute,
            $this->compileRouteStub(),
            FILE_APPEND
        );
    }

    /**
     * Compile route stub.
     *
     * @return string
     */
    protected function compileRouteStub()
    {
        $name = $this->argument('name');

        return str_replace(
            ['DummyPluralModel', 'DummyController'],
            [strtolower(str_plural($name)), $this->controllerName($name)],
            file_get_contents($this->getRouteStub())
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
     * Get route stub.
     *
     * @return string
     */
    protected function getRouteStub()
    {
        return __DIR__ . '/stubs/make/routes.stub';
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
            'name' => $modelName,
        ]);
    }

    /**
     * Make views.
     */
    protected function makeViews()
    {
        $name = $this->getNameInput();
        $module = $this->getModuleName();
        $force = $this->option('force');

        $this->call('guesl:view', [
            'name' => $name,
            '--module' => $module,
            '--force' => $force,
        ]);
    }

    /**
     * Make assets.
     */
    protected function makeAssets()
    {
        $name = $this->getNameInput();
        $module = $this->getModuleName();
        $force = $this->option('force');

        $this->call('guesl:js', [
            'name' => $name,
            '--module' => $module,
            '--force' => $force,
        ]);
    }
}
