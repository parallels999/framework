<?php

namespace Illuminate\Tests\Integration\Generators;

use Illuminate\Console\Generators\PresetManager;
use Illuminate\Console\Generators\Presets\Laravel;

class ObserverMakeCommandTest extends TestCase
{
    protected $files = [
        'app/Observers/FooObserver.php',
    ];

    public function testItCanGenerateObserverFile()
    {
        $this->artisan('make:observer', ['name' => 'FooObserver'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Observers;',
            'class FooObserver',
        ], 'app/Observers/FooObserver.php');
    }

    public function testItCanGenerateObserverFileWithModel()
    {
        $this->artisan('make:observer', ['name' => 'FooObserver', '--model' => 'Foo'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Observers;',
            'use App\Models\Foo;',
            'class FooObserver',
            'public function created(Foo $foo)',
            'public function updated(Foo $foo)',
            'public function deleted(Foo $foo)',
            'public function restored(Foo $foo)',
            'public function forceDeleted(Foo $foo)',
        ], 'app/Observers/FooObserver.php');
    }

    public function testItCanGenerateObserverFileWithCustomNamespacedModel()
    {
        $manager = $this->app->make(PresetManager::class);

        $manager->extend('acme', fn () => new class('App', $this->app->basePath(), $this->app->make('config')) extends Laravel {
            public function modelNamespace() {
                return 'Acme\Model';
            }
        });

        $this->artisan('make:observer', ['name' => 'FooObserver', '--model' => 'Foo', '--preset' => 'acme'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\Observers;',
            'use Acme\Model\Foo;',
            'class FooObserver',
            'public function created(Foo $foo)',
            'public function updated(Foo $foo)',
            'public function deleted(Foo $foo)',
            'public function restored(Foo $foo)',
            'public function forceDeleted(Foo $foo)',
        ], 'app/Observers/FooObserver.php');
    }
}
