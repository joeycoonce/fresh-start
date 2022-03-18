<?php

namespace JoeyCoonce\FreshStart\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use JoeyCoonce\FreshStart\Helpers;
use Symfony\Component\Process\Process;

class FreshStartCommand extends Command
{
    public $signature = 'fresh-start
                            {--composer=global} : Absolute path to the Composer binary which should be used to install packages
                            {--livewire=false} : Installs Livewire
                            {--backpack=true} ; Installs backpack';

    public $description = 'My command';

    public function handle(): int
    {

        // dd($this->option('composer'), $this->option('livewire'), $this->option('backpack'));
        // Configure Session...
        $this->configureSession();

        // Install scaffolding...
        $this->scaffold();

        // Update User table and factory (must be done before backpack install)
        $this->database();

        // Install Spatie Permission
        $this->permission();

        // Install Backpack
        if ($this->optionAsBool('backpack')) {
            $this->info('Installing Backpack');
            $this->backpack();
        }

        // Install Livewire
        if ($this->optionAsBool('livewire')) {
            $this->info('Installing Livewire');
            $this->livewire();
        }

        // Reconfigure
        $this->reconfigure();

        $this->callSilent('vendor:publish', ['--tag' => 'fresh-start-config']);

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

        $stubsPath = Helpers::getStubsPath();
        (new Filesystem())->copyDirectory($stubsPath.'/app/Http/Controllers/Auth', app_path('Http/Controllers/Auth'));
        (new Filesystem())->copyDirectory($stubsPath.'/app/Http/Requests/Auth', app_path('Http/Requests/Auth'));

        $this->info('Scaffolding installed successfully.');
    }

    protected function database()
    {
        $stubsPath = Helpers::getStubsPath();
        (new Filesystem())->copyDirectory($stubsPath.'/database/migrations', database_path('migrations'));
        (new Filesystem())->copyDirectory($stubsPath.'/database/factories', database_path('factories'));
    }

    protected function permission()
    {
        $this->requireComposerPackages('backpack/permissionmanager');
        $this->callSilent('vendor:publish', ['--provider' => 'Spatie\Permission\PermissionServiceProvider']);

        $stubsPath = Helpers::getStubsPath();
        (new Filesystem())->copyDirectory($stubsPath.'/app/Http/Middleware', app_path('Http/Middleware'));
        (new Filesystem())->copy($stubsPath.'/app/Providers/AuthServiceProvider.php', app_path('Providers/AuthServiceProvider.php'));

//         // Update config...
//         Helpers::replaceInFile('];', "
//     /*
//     * Custom
//     */
//     'super_admin_role' => 'Super Admin',
// ];", config_path('permission.php'));
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
        if ((new Filesystem())->exists(resource_path('lang'))) {
            // (new Filesystem)->ensureDirectoryExists(base_path('lang'));
            (new Filesystem())->copyDirectory(resource_path('lang'), base_path('lang'));
            (new Filesystem())->deleteDirectory(resource_path('lang'));
        }

        $stubsPath = Helpers::getStubsPath();
        (new Filesystem())->copyDirectory($stubsPath.'/app/Http/Controllers/Admin', app_path('Http/Controllers/Admin'));
        (new Filesystem())->copyDirectory($stubsPath.'/app/Http/Requests/Admin', app_path('Http/Requests/Admin'));
        (new Filesystem())->copy($stubsPath.'/app/Providers/AppServiceProvider.php', app_path('Providers/AppServiceProvider.php'));

        Helpers::replaceInFile("'guard' => 'backpack',", "'guard' => null,", config_path('backpack/base.php'));
//         Helpers::replaceInFile('
        // ];', "
//     /*
//     |--------------------------------------------------------------------------
//     | Custom
//     |--------------------------------------------------------------------------
//     |
//     */
//     'backpack_access_permission' => 'access backend',

        // ];", config_path('backpack/permissionmanager.php'));

        $this->callSilent('migrate', ['--no-interaction' => true]);
    }

    protected function livewire()
    {
        $this->requireComposerPackages('livewire/livewire');
        Helpers::replaceInFile("</head>", "
        @livewireStyles
    </head>", resource_path('views/layouts/app.blade.php'));

        Helpers::replaceInFile("</body>", "
        @livewireScripts
    </head>", resource_path('views/layouts/app.blade.php'));

        Helpers::replaceInFile("</head>", "
        @livewireStyles
    </head>", resource_path('views/layouts/guest.blade.php'));

        Helpers::replaceInFile("</body>", "
        @livewireScripts
    </head>", resource_path('views/layouts/guest.blade.php'));
    }

    /*
     * Reconfigure and customize scaffolded code
    */
    protected function reconfigure()
    {
        $stubsPath = Helpers::getStubsPath();

        // Copy stubs
        (new Filesystem())->copyDirectory($stubsPath.'/resources/views', resource_path('views'));
        (new Filesystem())->copyDirectory($stubsPath.'/app/Models', app_path('Models'));
        (new Filesystem())->copyDirectory($stubsPath.'/tests', base_path('tests'));

        // Install Helper Provider...
        // (new Filesystem)->ensureDirectoryExists(app_path('Helpers'));
        (new Filesystem())->copyDirectory($stubsPath.'/app/Helpers', app_path('Helpers'));
        (new Filesystem())->copy($stubsPath.'/app/Providers/HelperServiceProvider.php', app_path('Providers/HelperServiceProvider.php'));
        Helpers::installServiceProviderAfter('RouteServiceProvider', 'HelperServiceProvider');

        // Restructure status alerts
        if ((new Filesystem())->exists(resource_path('/views/components/auth-session-status.blade.php'))) {
            (new Filesystem())->delete(resource_path('/views/components/auth-session-status.blade.php'));
        }
        if ((new Filesystem())->exists(resource_path('/views/components/auth-validation-errors.blade.php'))) {
            (new Filesystem())->delete(resource_path('/views/components/auth-validation-errors.blade.php'));
        }
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

    protected function optionAsBool(string $key): bool
    {
        $option = $this->option($key);

        return ($option === null) || filter_var($option, FILTER_VALIDATE_BOOLEAN);
    }
}
