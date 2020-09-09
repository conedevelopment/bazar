<?php

namespace Bazar\Support;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

abstract class Scaffold
{
    /**
     * Install the scaffold.
     *
     * @return void
     */
    public static function install(): void
    {
        static::updatePackages();
        static::updateMixFile();
        static::removeNodeModules();
    }

    /**
     * Update the "package.json" file.
     *
     * @param  bool  $dev
     * @return void
     */
    protected static function updatePackages(bool $dev = true): void
    {
        if (! is_file(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = static::updatePackageArray(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : []
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    /**
     * Remove the installed Node modules.
     *
     * @return void
     */
    protected static function removeNodeModules(): void
    {
        tap(new Filesystem, function ($files) {
            $files->deleteDirectory(base_path('node_modules'));

            $files->delete(base_path('yarn.lock'));
        });
    }

    /**
     * Update the given package array.
     *
     * @param  array  $packages
     * @return array
     */
    protected static function updatePackageArray(array $packages): array
    {
        return [
            '@inertiajs/inertia' => '^0.2.1',
            '@inertiajs/inertia-vue' => '^0.2.1',
            'bootstrap' => '^4.5.2',
            'chart.js' => '^2.9.3',
            'quill' => '^1.3.7',
            'simplebar' => '^5.2.1',
            'vue' => '^2.6.12',
            'vue-template-compiler' => '^2.6.12'
        ] + $packages;
    }

    /**
     * Update the bootstrapping files.
     *
     * @return void
     */
    protected static function updateMixFile(): void
    {
        $stub = file_get_contents(__DIR__.'/../../stubs/webpack.mix.js.stub');

        if (is_file(base_path('webpack.mix.js'))
            && ! Str::contains(file_get_contents(base_path('webpack.mix.js')), $stub)) {
            file_put_contents(base_path('webpack.mix.js'), $stub, FILE_APPEND);
        }
    }
}
