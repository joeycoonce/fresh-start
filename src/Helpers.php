<?php

namespace JoeyCoonce\FreshStart;

use Illuminate\Support\Str;

class Helpers
{
    // /**
    //  * Update the "package.json" file.
    //  *
    //  * @param  callable  $callback
    //  * @param  bool  $dev
    //  * @return void
    //  */
    // public static function updateNodePackages(callable $callback, $dev = true)
    // {
    //     if (! file_exists(base_path('package.json'))) {
    //         return;
    //     }

    //     $configurationKey = $dev ? 'devDependencies' : 'dependencies';

    //     $packages = json_decode(file_get_contents(base_path('package.json')), true);

    //     $packages[$configurationKey] = $callback(
    //         array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
    //         $configurationKey
    //     );

    //     ksort($packages[$configurationKey]);

    //     file_put_contents(
    //         base_path('package.json'),
    //         json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
    //     );
    // }

//     /**
//      * Delete the "node_modules" directory and remove the associated lock files.
//      *
//      * @return void
//      */
//     public static function flushNodeModules()
//     {
//         tap(new Filesystem, function ($files) {
//             $files->deleteDirectory(base_path('node_modules'));

//             $files->delete(base_path('yarn.lock'));
//             $files->delete(base_path('package-lock.json'));
//         });
//     }

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    public static function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * Returns the path to the correct test stubs.
     *
     * @return string
     */
    public static function getStubsPath()
    {
        return __DIR__.'/stubs';
    }

    /**
     * Install the service provider in the application configuration file.
     *
     * @param  string  $after
     * @param  string  $name
     * @return void
     */
    public static function installServiceProviderAfter($after, $name)
    {
        if (! Str::contains($appConfig = file_get_contents(config_path('app.php')), 'App\\Providers\\'.$name.'::class')) {
            file_put_contents(config_path('app.php'), str_replace(
                'App\\Providers\\'.$after.'::class,',
                'App\\Providers\\'.$after.'::class,'.PHP_EOL.'        App\\Providers\\'.$name.'::class,',
                $appConfig
            ));
        }
    }

}