<?php

namespace Guesl\Admin\Console\Commands;

use Illuminate\Console\Command;

class AuthMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guesl:auth
                    {--views : Only scaffold the authentication views}
                    {--force : Overwrite existing views by default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic login and registration views and routes';

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'auth/login.blade.stub' => 'auth/login.blade.php',
        'auth/passwords/email.blade.stub' => 'auth/passwords/email.blade.php',
        'auth/passwords/reset.blade.stub' => 'auth/passwords/reset.blade.php',
        'auth/incs/foot.blade.stub' => 'auth/incs/foot.blade.php',
        'auth/incs/head.blade.stub' => 'auth/incs/head.blade.php',
        'auth/layouts/app.blade.stub' => 'auth/layouts/app.blade.php',
    ];

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

        if (!$this->option('views')) {
            file_put_contents(
                app_path('Http/Controllers/Admin/HomeController.php'),
                $this->compileControllerStub()
            );

            file_put_contents(
                base_path('routes/admin.php'),
                file_get_contents(__DIR__ . '/stubs/make/routes.stub'),
                FILE_APPEND
            );
        }

        $this->info('Authentication scaffolding generated successfully.');
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {

        if (!is_dir($directory = app_path('Http/Controllers/Admin'))) {
            mkdir($directory, 0755, true);
        }

        if (!is_dir($directory = resource_path('views/auth/layouts'))) {
            mkdir($directory, 0755, true);
        }

        if (!is_dir($directory = resource_path('views/auth/incs'))) {
            mkdir($directory, 0755, true);
        }

        if (!is_dir($directory = resource_path('views/auth/layouts'))) {
            mkdir($directory, 0755, true);
        }

        if (!is_dir($directory = resource_path('views/auth/passwords'))) {
            mkdir($directory, 0755, true);
        }

        if (!is_dir($directory = public_path('images'))) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $key => $value) {
            if (file_exists($view = resource_path('views/' . $value)) && !$this->option('force')) {
                if (!$this->confirm("The [{$value}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                __DIR__ . '/stubs/make/views/' . $key,
                $view
            );
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportAssets()
    {
        if (!file_exists(public_path('images/logo.png'))) {
            copy(
                __DIR__ . '/../../../public/images/logo.png',
                public_path('images/logo.png')
            );
        }

        if (is_dir(public_path('templates')) && !$this->option('force')) {
            if (!$this->confirm("The template assets already exists. Do you want to replace it?")) {
                return;
            }

            $this->rrmdir(public_path('templates'));
        }

        $this->recurseCopy(__DIR__ . '/../../../public/templates', public_path('templates'));
    }

    /**
     * Compiles the HomeController stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        return str_replace(
            'AppNamespace\\',
            $this->getAppNamespace(),
            file_get_contents(__DIR__ . '/stubs/make/controllers/HomeController.stub')
        );
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
     * Get the application namespace.
     *
     * @return string
     */
    protected function getAppNamespace()
    {
        return $this->laravel->getNamespace();
    }
}
