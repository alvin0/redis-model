<?php
namespace Alvin0\RedisModel\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class RedisModelMakerCommand extends GeneratorCommand
{
    public $signature = 'redis-model:model {name : The name of the redis model}';

    public $description = 'Create a new Redis model class.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/model.stub';
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return config('redis-model.commands.rootNamespace', 'App\\RedisModels');
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function getGenerationPath()
    {
        return config('redis-model.commands.generate_path', app_path('RedisModels'));
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->getGenerationPath() . str_replace('\\', '/', $name) . '.php';
    }

}
