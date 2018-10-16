<?php

namespace Guesl\Admin\Console\Commands;

use Guesl\Admin\Contracts\Constant;
use Illuminate\Support\Facades\DB;

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
        $this->exportCommonJs();
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
        $this->makeDirectory(resource_path('assets/admin/js/helpers'));
        $this->makeDirectory(resource_path('assets/admin/js/components'));
        $this->makeDirectory(resource_path('assets/admin/js/config'));
    }

    /**
     * Make or Update package json file.
     */
    protected function makePackageJson()
    {
        $packageJsonPath = base_path('package.json');
        if (file_exists($packageJsonPath)) {
            //if (!$this->confirm("The [{$packageJsonPath}] library config file already exists. Do you want to replace it?")) {
            return;
            // }
        }

        file_put_contents(
            $packageJsonPath,
            file_get_contents(__DIR__ . '/../../../package.json')
        );

        $this->info('Created: ' . $packageJsonPath);
    }

    /**
     * Export common js files.
     */
    protected function exportCommonJs()
    {
        $this->exportHelperJs();
        $this->exportComponents();
        $this->exportConfig();
    }

    /**
     * Export helper js files.
     */
    protected function exportHelperJs()
    {
        $axiosJs = resource_path('assets/admin/js/helpers/axios.js');

        if (file_exists($axiosJs)) {
            $this->error('AxiosJs file already exists.');
            return;
        }

        $template = $this->getTemplate();

        file_put_contents(
            $axiosJs,
            file_get_contents(__DIR__ . "/stubs/make/resources/${template}/assets/js/helpers/axios.js")
        );

        $toastJs = resource_path('assets/admin/js/helpers/toast.js');

        if (file_exists($toastJs)) {
            $this->error('Toast Js file already exists.');
        }

        file_put_contents(
            $toastJs,
            file_get_contents(__DIR__ . "/stubs/make/resources/${template}/assets/js/helpers/toast.js")
        );
    }

    /**
     * Export component files.
     */
    protected function exportComponents()
    {
        $template = $this->getTemplate();

        $dataTableComponent = resource_path('assets/admin/js/components/DataTableComponent.js');
        if (!file_exists($dataTableComponent)) {
            file_put_contents(
                $dataTableComponent,
                file_get_contents(__DIR__ . "/stubs/make/resources/${template}/assets/js/components/DataTableComponent.js")
            );
        }

        $comboComponent = resource_path('assets/admin/js/components/ComboComponent.js');
        if (!file_exists($comboComponent)) {
            file_put_contents(
                $comboComponent,
                file_get_contents(__DIR__ . "/stubs/make/resources/${template}/assets/js/components/ComboComponent.js")
            );
        }
    }

    /**
     * Export config files.
     */
    protected function exportConfig()
    {
        $configAppJs = resource_path('assets/admin/js/config/app.js');

        if (file_exists($configAppJs)) {
            $this->error(' app.js config file already exists.');
            return;
        }

        $template = $this->getTemplate();

        file_put_contents(
            $configAppJs,
            file_get_contents(__DIR__ . "/stubs/make/resources/${template}/assets/js/config/app.js")
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
        $defColumns = $this->getDefColumns();
        $defColumnsJSON = json_encode($defColumns);

        return str_replace(
            [
                'DummyTableId',
                'DummyResource',
                'DummyDefColumns'
            ],
            [
                $this->tableId(),
                str_plural(strtolower($this->getNameInput())),
                $defColumnsJSON,
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
     * Get the defined columns in the table js file.
     *
     * @return array
     */
    protected function getDefColumns()
    {
        $tableName = $this->getTableName();
        $tableMetas = DB::getSchemaBuilder()->getColumnListing($tableName);

        $defColumns = [];
        if (!empty($tableMetas)) {
            foreach ($tableMetas as $meta) {
                array_push($defColumns, [
                    'field' => $meta,
                    'title' => ucfirst(camel_case($meta)),
                    'sortable' => false,
                    'selector' => false,
                    'textAlign' => 'center',
                    'searchable' => true,
                ]);
            }
        }

        return $defColumns;
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
                $this->compileIndexJsConfig()
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
        $webpackPath = base_path('webpack.mix.js');
        $fileArray = $fileArray = file($webpackPath);

        if (!$this->isWebpackExists()) {
            $template = $this->getTemplate();
            $modelName = strtolower($this->getNameInput());

            $jsNewLine = str_replace(
                ['DummyModel'],
                [$modelName],
                file_get_contents(__DIR__ . "/stubs/make/resources/${template}/assets/webpack.mix.js")
            );

            array_push($fileArray, "\n");
            array_push($fileArray, "\n");
            array_push($fileArray, $jsNewLine);
        }

        return implode("", $fileArray);
    }

    /**
     * Check if the webpack js line exists.
     *
     * @return bool
     */
    protected function isWebpackExists()
    {
        $webpackPath = base_path('webpack.mix.js');
        $fileArray = $fileArray = file($webpackPath);

        $modelName = strtolower($this->getNameInput());

        $webpackExist = false;
        foreach ($fileArray as $line) {
            if (strpos($line, "/$modelName/") !== false) {
                $webpackExist = true;
                break;
            }
        }

        return $webpackExist;
    }
}
