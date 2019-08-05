<?php

namespace App\Console\Commands\Make\Traits;

trait CreateEntity
{
    public function createEntity(string $context): string
    {
        $this->comment('Creating Entity');
        $entityName = $this->ask('Name of entity');
        $entityFile = base_path() . '/flyt-core/' . $context . '/Domain/Entities/' . $entityName . '.php';

        while (file_exists(($entityFile))) {
            $entityName = $this->ask('The entity "' . $entityName . '" already exists! Please try again');
            $entityFile = base_path() . '/flyt-core/' . $context . '/Domain/Entities/' . $entityName . '.php';
        }

        $stub = file_get_contents(app_path('Console/Commands/Make/Templates/Entity.stub'));
        $stub = str_replace('{bounded_context}', $context, $stub);
        $stub = str_replace('{entity_name}', $entityName, $stub);

        file_put_contents($entityFile, $stub);
        $this->gitAdd($entityFile);

        $this->info('Entity created' . PHP_EOL);

        $this->createEntityTest($context, $entityName);

        return $entityName;
    }

    public function createEntityTest(string $context, string $entityName): void
    {
        $this->comment('Creating Entity Test');
        $entityTestFile = base_path() . '/flyt-core/' . $context . '/Tests/Domain/Entities/' . $entityName . 'Test.php';

        $stub = file_get_contents(app_path('Console/Commands/Make/Templates/TestEntity.stub'));
        $stub = str_replace('{bounded_context}', $context, $stub);
        $stub = str_replace('{camel_case_entity_name}', camel_case($entityName), $stub);
        $stub = str_replace('{entity_name}', $entityName, $stub);

        file_put_contents($entityTestFile, $stub);
        $this->gitAdd($entityTestFile);

        $this->info('Entity Test created');
    }
}