<?php

namespace Guesl\Admin\Console\Commands;

use Guesl\Admin\Contracts\Constant;
use Illuminate\Console\GeneratorCommand;

class AuthMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guesl:auth
                    {--template : Template name, "metronic" as default.}
                    {--force : Overwrite existing views by default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic login and registration views and routes';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->createDirectories();

        $this->exportViews();
        $this->exportAssets();
        $this->exportHomeController();
        $this->exportRoute();

        $this->info('Authentication scaffolding generated successfully.');
    }

    /**
     * Export the authentication route.
     *
     * @return void
     */
    protected function exportRoute()
    {
        $webRoutePath = base_path('routes/web.php');
        file_put_contents(
            $webRoutePath,
            file_get_contents(__DIR__ . '/stubs/make/routes.auth.stub'),
            FILE_APPEND
        );

        $this->info($webRoutePath . ' updated successfully.');
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        $this->makeDirectory(app_path('Http/Controllers/Admin'));
        $this->makeDirectory(resource_path('views/auth/layouts'));
        $this->makeDirectory(resource_path('views/auth/incs'));
        $this->makeDirectory(resource_path('views/auth/passwords'));
        $this->makeDirectory(public_path('images'));
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
            "templates/{$template}/auth/login.blade.stub" => "auth/login.blade.php",
            "templates/{$template}/auth/register.blade.stub" => "auth/register.blade.php",
            "templates/{$template}/auth/passwords/email.blade.stub" => "auth/passwords/email.blade.php",
            "templates/{$template}/auth/passwords/reset.blade.stub" => "auth/passwords/reset.blade.php",
            "templates/{$template}/auth/incs/foot.blade.stub" => "auth/incs/foot.blade.php",
            "templates/{$template}/auth/incs/head.blade.stub" => "auth/incs/head.blade.php",
            "templates/{$template}/auth/layouts/app.blade.stub" => "auth/layouts/app.blade.php",
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

            $this->info(resource_path('views/' . $value) . ' generated successfully.');
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportAssets()
    {
        if (!file_exists($logoPath = public_path('images/gu.png'))) {
            copy(
                __DIR__ . '/../../../public/images/gu.png',
                $logoPath
            );
        }

        if (!file_exists($logoPath = public_path('images/guesl.png'))) {
            copy(
                __DIR__ . '/../../../public/images/guesl.png',
                $logoPath
            );
        }

        if (!file_exists($logoPath = public_path('images/guesl-blue.png'))) {
            copy(
                __DIR__ . '/../../../public/images/guesl-blue.png',
                $logoPath
            );
        }

        if (!file_exists($logoPath = public_path('images/guesl-purple.png'))) {
            copy(
                __DIR__ . '/../../../public/images/guesl-purple.png',
                $logoPath
            );
        }

        if (!file_exists($logoPath = public_path('images/guesl-white.png'))) {
            copy(
                __DIR__ . '/../../../public/images/guesl-white.png',
                $logoPath
            );
        }

        $this->info('Assets images generated successfully.');

        if (is_dir($templatesDir = public_path('templates')) && !$this->option('force')) {
            if (!$this->confirm("The template assets already exists. Do you want to replace it?")) {
                return;
            }

            $this->rrmdir($templatesDir);
        }

        $this->recurseCopy(__DIR__ . '/../../../public/templates', $templatesDir);

        $this->info($templatesDir . ' directory generated successfully.');
    }

    /**
     * Export the HomeController.
     *
     * @return void
     */
    protected function exportHomeController()
    {
        $homeControllerPath = app_path('Http/Controllers/Admin/HomeController.php');
        file_put_contents(
            $homeControllerPath,
            $this->compileControllerStub()
        );

        $this->info($homeControllerPath . ' generated successfully.');
    }

    /**
     * Compiles the HomeController stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        return str_replace(
            ['DummyNamespace', 'DummyRootNamespace'],
            [$this->controllerNamespace(), $this->rootNamespace()],
            file_get_contents($this->getControllerStub())
        );
    }

    /**
     * Get the controller stub file for the generator.
     *
     * @return string
     */
    protected function getControllerStub()
    {
        return __DIR__ . '/stubs/make/controllers/HomeController.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @return string
     */
    protected function controllerNamespace()
    {
        return $this->getNamespace($this->rootNamespace() . 'Http/Controllers/Admin');
    }

    /**
     * Recurse copy entire directory.
     *
     * @param string $src
     * @param string $dst
     */
    protected function recurseCopy($src, $dst)
    {
        $dir = opendir($src);

        mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Recurse remove entire directory.
     *
     * @param string $src
     */
    protected function rrmdir($src)
    {
        $dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $full = $src . '/' . $file;
                if (is_dir($full)) {
                    $this->rrmdir($full);
                } else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);
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
