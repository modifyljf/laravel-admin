<?php

namespace Guesl\Admin\Console\Commands;

use Guesl\Admin\Contracts\Constant;
use Illuminate\Console\GeneratorCommand;

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
            "templates/{$template}/admin/incs/navigator.blade.stub" => "admin/incs/navigator.blade.php",
            "templates/{$template}/admin/layouts/app.blade.stub" => "admin/layouts/app.blade.php",
            "templates/{$template}/home.blade.stub" => "admin/home.blade.php",
        ];
    }

    /**
     * Export the authentication views.
     *
     * @return void
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
