<?php

namespace JoeyCoonce\FreshStart\Commands;

use JoeyCoonce\FreshStart\Helpers;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Illuminate\Console\Command;

class FreshStartCommand extends Command
{
    public $signature = 'fresh-start {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    public $description = 'My command';

    public function handle(): int
    {
        // Configure Session...
        $this->configureSession();

        // Install scaffolding...
        $this->scaffold();

        // Update User table and factory (must be done before backpack install)
        $this->database();

        // Install Spatie Permission
        $this->permission();

        // Install Backpack
        $this->backpack();

        // Reconfigure
        $this->reconfigure();
        
        $this->comment('All done');

        $this->line('');
        
        $this->comment('Please execute the "npm install && npm run dev" command to build your assets.');

        return self::SUCCESS;
    }

    /**
     * Configure the session driver as database.
     *
     * @return void
     */
    protected function configureSession()
    {
        if (! class_exists('CreateSessionsTable')) {
            try {
                $this->call('session:table');
            } catch (Exception $e) {
                //
            }
        }

        Helpers::replaceInFile("'SESSION_DRIVER', 'file'", "'SESSION_DRIVER', 'database'", config_path('session.php'));
        Helpers::replaceInFile('SESSION_DRIVER=file', 'SESSION_DRIVER=database', base_path('.env'));
        Helpers::replaceInFile('SESSION_DRIVER=file', 'SESSION_DRIVER=database', base_path('.env.example'));
    }

    /*
     * Install scaffolding: Breeze with Bootstrap
    */
    protected function scaffold()
    {
        // Install Breeze, swap for Bootstrap
        $this->callSilent('breeze:install');
        $this->callSilent('jetstrap:swap', ['stack' => 'breeze']);

        $this->removeNpmPackages(['tailwindcss', '@tailwindcss/forms']);

        $this->info('Scaffolding installed successfully.');
    }

    protected function database()
    {
        $stubsPath = Helpers::getStubsPath();
        (new Filesystem)->copyDirectory($stubsPath.'/database/migrations', database_path('migrations'));
        (new Filesystem)->copyDirectory($stubsPath.'/database/factories', database_path('factories'));
    }

    protected function permission()
    {
        $this->requireComposerPackages('backpack/permissionmanager');
        $this->callSilent('vendor:publish', ['--provider' => 'Spatie\Permission\PermissionServiceProvider']);
    }

    protected function backpack()
    {
        // Install Laravel Backpack
        $this->requireComposerPackages('backpack/crud:^5.0');
        $this->requireComposerPackages('backpack/generators', '--dev');
        $this->callSilent('backpack:install', ['--no-interaction' => true]);

        $this->requireComposerPackages('backpack/permissionmanager');
        $this->callSilent('vendor:publish', ['--provider' => 'Backpack\PermissionManager\PermissionManagerServiceProvider']);

        // In case Backpack publishes lang to resources/lang...
        if((new Filesystem)->exists(resource_path('lang')))
        {
            // (new Filesystem)->ensureDirectoryExists(base_path('lang'));
            (new Filesystem)->copyDirectory(resource_path('lang'), base_path('lang'));
            (new Filesystem)->deleteDirectory(resource_path('lang'));
        }

        $this->callSilent('migrate', ['--no-interaction' => true]);
    }

    /*
     * Reconfigure and customize scaffolded code
    */
    protected function reconfigure()
    {
        $stubsPath = Helpers::getStubsPath();

        // Ensure Directories...
        (new Filesystem)->ensureDirectoryExists(app_path('Helpers'));

        // Copy stubs
        (new Filesystem)->copyDirectory($stubsPath.'/resources/views', resource_path('views'));
        (new Filesystem)->copyDirectory($stubsPath.'/app/Http/Controllers', app_path('Http/Controllers'));
        (new Filesystem)->copyDirectory($stubsPath.'/app/Http/Requests', app_path('Http/Requests'));
        (new Filesystem)->copyDirectory($stubsPath.'/app/Http/Middleware', app_path('Http/Middleware'));
        (new Filesystem)->copyDirectory($stubsPath.'/app/Helpers', app_path('Helpers'));
        (new Filesystem)->copyDirectory($stubsPath.'/app/Models', app_path('Models'));
        (new Filesystem)->copyDirectory($stubsPath.'/app/Providers', app_path('Providers'));

        // Install Helper Provider...
        Helpers::installServiceProviderAfter('RouteServiceProvider', 'HelperServiceProvider');

        // Restructure status alerts
        if ((new Filesystem)->exists(resource_path('/views/components/auth-session-status.blade.php'))) {
            (new Filesystem)->delete(resource_path('/views/components/auth-session-status.blade.php'));
        }
        if ((new Filesystem)->exists(resource_path('/views/components/auth-validation-errors.blade.php'))) {
            (new Filesystem)->delete(resource_path('/views/components/auth-validation-errors.blade.php'));
        }

        // Update configs... 
        Helpers::replaceInFile("'guard' => 'backpack',", "'guard' => null,", config_path('backpack/base.php'));

        Helpers::replaceInFile('
];', "
    /*
    |--------------------------------------------------------------------------
    | Custom
    |--------------------------------------------------------------------------
    |
    */
    'backpack_access_permission' => 'access backend',
    
];", config_path('backpack/permissionmanager.php'));

        Helpers::replaceInFile('];', "
    /*
    * Custom
    */
    'super_admin_role' => 'Super Admin',
];", config_path('permission.php'));

    }

    /**
     * Installs the given Composer Packages into the application.
     *
     * @param  mixed  $packages
     * @return void
     */
    protected function requireComposerPackages($packages)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }

        /**
     * Removes the given NPM Packages from the application.
     *
     * @param  mixed  $packages
     * @return void
     */
    protected function removeNpmPackages($packages)
    {
        $command = array_merge(
            ['npm', 'remove'],
            is_array($packages) ? $packages : func_get_args()
        );

        (new Process($command, base_path()))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }
}
