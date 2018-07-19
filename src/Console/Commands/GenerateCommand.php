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
     * Make views.
     */
    protected function makeViews()
    {
        $this->makeIndexView();
        $this->makeEditView();

        $this->info('Generated: Index and Edit views.');
    }

    /**
     * Make index view.
     */
    protected function makeIndexView()
    {
        $name = strtolower($this->getNameInput());

        $indexViewPath = resource_path('views/admin/models/' . $name . '/index.blade.php');

        if (file_exists($indexViewPath) && !$this->option('force')) {
            if (!$this->confirm("The [{$indexViewPath}] view already exists. Do you want to replace it?")) {
                return;
            }
        }

        file_put_contents(
            $indexViewPath,
            $this->compileIndexViewStub()
        );

        $this->info('Created: ' . $indexViewPath);
    }

    /**
     * Compile index view stub.
     *
     * @return string
     */
    protected function compileIndexViewStub()
    {
        $name = $this->getNameInput();
        $module = $this->option('module');

        $menuName = ucfirst($name);
        $moduleName = $module ? ucfirst($module) . ' Module' : null;
        $createUrl = str_plural(strtolower($name)) . '/create';
        $tableId = $this->tableId();

        if ($moduleName) {
            return str_replace(
                [
                    'DummyMenuName',
                    'DummyModuleName',
                    'DummyCreateURL',
                    'DummyLowerModel',
                    'DummyTableId'
                ],
                [$menuName, $moduleName, $createUrl, strtolower($name), $tableId],
                file_get_contents($this->getIndexModuleViewStub())
            );
        } else {
            return str_replace(
                [
                    'DummyMenuName',
                    'DummyCreateURL',
                    'DummyLowerModel',
                    'DummyTableId'
                ],
                [$menuName, $createUrl, strtolower($name), $tableId],
                file_get_contents($this->getIndexViewStub())
            );
        }
    }

    /**
     * Get edit view stub.
     *
     * @param string
     * @return string
     */
    protected function getIndexViewStub()
    {
        return $this->getTemplateViewStub() . "/model/index.blade.stub";
    }

    /**
     * Get edit view stub.
     *
     * @param string
     * @return string
     */
    protected function getIndexModuleViewStub()
    {
        return $this->getTemplateViewStub() . "/model/index.module.blade.stub";
    }

    /**
     * Make edit view.
     */
    protected function makeEditView()
    {
        $name = strtolower($this->getNameInput());

        $editViewPath = resource_path('views/admin/models/' . $name . '/edit.blade.php');

        if (file_exists($editViewPath) && !$this->option('force')) {
            if (!$this->confirm("The [{$editViewPath}] view already exists. Do you want to replace it?")) {
                return;
            }
        }

        file_put_contents(
            $editViewPath,
            file_get_contents($this->getEditViewStub())
        );

        $this->info('Created: ' . $editViewPath);
    }

    /**
     * Get edit view stub.
     *
     * @param string
     * @return string
     */
    protected function getEditViewStub()
    {
        return $this->getTemplateViewStub() . "/model/edit.blade.stub";
    }

    /**
     * Make navigator item.
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
        $this->makePublicJs();
        $this->makeResourceJs();
    }

    /**
     * Make js file under public folder.
     */
    protected function makePublicJs()
    {
        $name = strtolower($this->getNameInput());
        $assetModel = strtolower($name);

        $indexJsPath = public_path('admin/js/' . $assetModel . '/index.js');
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
     * Make js file under resource folder.
     */
    protected function makeResourceJs()
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
            ],
            [
                $this->tableId(),
            ],
            file_get_contents($stub)
        );
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
     * Get the template view stub.
     *
     * @return string
     */
    protected function getIndexJsStub()
    {
        $template = $this->option('template') ?: Constant::TEMPLATE_DEFAULT;

        return __DIR__ . "/stubs/make/resources/${template}/assets/js/index.js.stub";
    }

    /**
     * Get the table id by 'name' argument.
     *
     * @return string
     */
    protected function tableId()
    {
        $name = $this->argument('name');
        $tableId = camel_case(str_replace(' ', '_', strtolower($name)) . 'Table');

        return $tableId;
    }
}
