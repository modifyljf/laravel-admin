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
                app_path('Http/Controllers/HomeController.php'),
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
        if (is_dir(public_path('template')) && !$this->option('force')) {
            if (!$this->confirm("The template assets already exists. Do you want to replace it?")) {
                return;
            }
        }

        $this->recurseCopy(__DIR__ . '/../../../public', public_path());
    }

    /**
     * Compiles the HomeController stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        return str_replace(
            '{{namespace}}',
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
                    recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
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
