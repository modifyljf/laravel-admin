<?php

namespace Guesl\Admin\Console\Commands;

use Guesl\Admin\Contracts\Constant;

class ViewMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guesl:view 
                    {name : The name of model.}
                    {--template : Template name, "metronic" as default.}
                    {--force : Overwrite existing objects by default.}
                    {--module= : The module which the `name` belongs to.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic admin views, index and create blade views.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->createDirectories();

        $this->makeViews();
        $this->makeNavItem();

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
        $module = $this->getModuleName();
        $moduleName = ucfirst($module);

        $navigator = resource_path('views/admin/incs/navigator.blade.php');
        $navigatorArray = file($navigator);

        if ($this->isModuleExists()) {
            foreach ($navigatorArray as $key => $line) {
                if (strpos($line, "GueslAdmin{$moduleName}SubMenuItem") !== false) {
                    array_splice(
                        $navigatorArray,
                        $key,
                        1,
                        $this->compileNavItemStub()
                    );
                    break;
                }
            }
        } else {
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
        $name = $this->getNameInput();
        $module = $this->getModuleName();


        $stub = $this->getTemplateViewStub();
        if ($module) {
            if ($this->isModuleExists()) {
                $stub .= '/admin/incs/submenuitem.blade.stub';
            } else {
                $stub .= '/admin/incs/navitem.sub.blade.stub';
            }
        } else {
            $stub .= '/admin/incs/navitem.blade.stub';
        }

        $menuName = ucfirst($name);
        $moduleName = ucfirst($module);
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
     * If the module exists, return true;
     * Else return false;
     *
     * @return string|null
     */
    protected function isModuleExists()
    {
        $module = $this->getModuleName();
        $moduleName = ucfirst($module);

        $navigator = resource_path('views/admin/incs/navigator.blade.php');
        $navigatorArray = file($navigator);

        $flag = false;
        foreach ($navigatorArray as $key => $line) {
            if (strpos($line, "GueslAdmin{$moduleName}SubMenuItem") !== false) {
                $flag = true;
                break;
            }
        }

        return $flag;
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
}
