<?php

namespace Guesl\Admin\Console\Commands;

use Guesl\Admin\Contracts\Constant;

class AdminMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guesl:admin
                    {--template : Template name, "metronic" as default.}
                    {--force : Overwrite existing views by default}';

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

        $this->exportViews();
        $this->exportNavigator();

        $this->info('Successful: Admin views generated.');
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        $this->makeDirectory(resource_path('views/admin/layouts'));
        $this->makeDirectory(resource_path('views/admin/incs'));
    }

    /**
     * Get The views that need to be exported.
     *
     * @return array
     */
    protected function getViews()
    {
        $template = $this->option('template') ?: Constant::TEMPLATE_DEFAULT;

        return [
            "templates/{$template}/admin/incs/foot.blade.stub" => "admin/incs/foot.blade.php",
            "templates/{$template}/admin/incs/footer.blade.stub" => "admin/incs/footer.blade.php",
            "templates/{$template}/admin/incs/head.blade.stub" => "admin/incs/head.blade.php",
            "templates/{$template}/admin/incs/header.blade.stub" => "admin/incs/header.blade.php",
            "templates/{$template}/admin/layouts/app.blade.stub" => "admin/layouts/app.blade.php",
            "templates/{$template}/home.blade.stub" => "admin/home.blade.php",
        ];
    }

    /**
     * Export the authentication views.
     */
    protected function exportViews()
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
     * Export the authentication views.
     */
    protected function exportNavigator()
    {
        $navigatorPath = resource_path('views/admin/incs/navigator.blade.php');

        if (!file_exists($navigatorPath)) {
            file_put_contents(
                $navigatorPath,
                $this->compileNavigatorStub()
            );

            $this->info('Generated: ' . $navigatorPath);
        }
    }

    /**
     * Compiles the HomeController stub.
     *
     * @return string
     */
    protected function compileNavigatorStub()
    {
        return str_replace(
            ['DummyRootNamespace'],
            $this->rootNamespace(),
            file_get_contents($this->getNavigatorStub())
        );
    }

    /**
     * Get navigator stub.
     *
     * @return string
     */
    protected function getNavigatorStub()
    {
        $template = $this->option('template') ?: Constant::TEMPLATE_DEFAULT;

        return __DIR__ . "/stubs/make/views/templates/$template/admin/incs/navigator.blade.stub";
    }

    /**
     * Compiles the HomeController stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        return str_replace(
            'AppNameSpace',
            $this->rootNamespace(),
            file_get_contents(__DIR__ . '/stubs/make/controllers/HomeController.stub')
        );
    }
}
