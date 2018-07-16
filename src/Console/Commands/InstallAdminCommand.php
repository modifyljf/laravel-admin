<?php

namespace Guesl\Admin\Console\Commands;

use Illuminate\Console\Command;

class InstallAdminCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'guesl:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the admin package';

    /**
     * Install directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->initDatabase();
        $this->call('guesl:auth');
    }

    /**
     * Create tables and seed it.
     *
     * @return void
     */
    public function initDatabase()
    {
        $this->call('migrate');
    }
}
