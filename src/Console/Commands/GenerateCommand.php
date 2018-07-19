<?php

namespace Guesl\Admin\Console\Commands;

use Guesl\Admin\Contracts\Constant;

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
                    {--force : Overwrite existing objects by default.}
                    {--module= : The module which the `name` belongs to.}';

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
        $this->makeRoute();
        $this->makeModel();

        $this->makeViews();
        $this->makeNavItem();

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
        $module = $this->option('module');

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
            "{$template}/model/index.blade.stub" => "$name/index.blade.php",
            "{$template}/model/edit.blade.stub" => "$name/edit.blade.php",
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
            if (file_exists($view = resource_path('views/admin/models/' . $value)) && !$this->option('force')) {
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
     * Make navigator item.
     *
     * @return void
     */
    protected function makeNavItem()
    {
        $navigator = resource_path('views/admin/incs/navigator.blade.php');
        $navigatorArray = file($navigator);

        foreach ($navigatorArray as $key => $line) {
            if (strpos($line, 'GueslAdminNavigatorMenuItemBlock') !== false) {
                array_splice(
                    $navigatorArray,
                    $key,
                    1,
                    $this->compileNavItemStub()
                );
                break;
            }
        }

        file_put_contents(
            $navigator,
            implode("", $navigatorArray)
        );

        $this->info("Updated: $navigator.");
    }

    /**
     * Compiles navitem blade stub.
     *
     * @return string
     */
    protected function compileNavItemStub()
    {
        $name = $this->argument('name');
        $module = $this->option('module');

        $stub = $this->getTemplateViewStub();
        if ($module) {
            $stub .= '/admin/incs/navitem.sub.blade.stub';
        } else {
            $stub .= '/admin/incs/navitem.blade.stub';
        }

        $menuName = ucfirst($name);
        $moduleName = ucfirst($module) . ' Module';
        $indexUrl = str_plural(strtolower($name));

        return str_replace(
            [
                'DummyMenuName',
                'DummyModuleName',
                'DummyIndexURL',
                'DummyRootNamespace',
                'DummyModuleConstant',
                'DummyMenuConstant'
            ],
            [
                $menuName,
                $moduleName,
                $indexUrl,
                $this->rootNamespace(),
                $this->moduleConstantName(),
                $this->menuConstantName()
            ],
            file_get_contents($stub)
        );
    }

    /**
     * Get module constant name.
     *
     * @return string|null
     */
    protected function moduleConstantName()
    {
        $moduleConstantName = null;

        $module = $this->option('module');

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

        $name = $this->argument('name');
        $module = $this->option('module');

        if ($module) {
            $menuConstantName = strtoupper("MENU_{$module}_$name");
        } else {
            $menuConstantName = strtoupper("MENU_$name");
        }

        return $menuConstantName;
    }

    /**
     * Get the template view stub.
     *
     * @return string
     */
    protected function getTemplateViewStub()
    {
        $template = $this->option('template') ?: Constant::TEMPLATE_DEFAULT;

        return __DIR__ . "/stubs/make/views/templates/${template}";
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
            "{$template}/assets/js/index.js.stub" => "admin/js/$name/index.js",
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
}
