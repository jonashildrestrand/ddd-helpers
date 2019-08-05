<?php

namespace App\Console\Commands\Make\Traits;

trait CreateValueObject
{
    public function createValueObject(string $context): string
    {
        $this->comment('Creating ValueObject');
        $valueObjectName = $this->ask('Name of valueObject');
        $valueObjectFile = base_path() . '/flyt-core/' . $context . '/Domain/ValueObjects/' . $valueObjectName . '.php';

        while (file_exists(($valueObjectFile))) {
            $valueObjectName = $this->ask('The valueObject "' . $valueObjectName . '" already exists! Please try again');
            $valueObjectFile = base_path() . '/flyt-core/' . $context . '/Domain/ValueObjects/' . $valueObjectName . '.php';
        }

        $stub = file_get_contents(app_path('Console/Commands/Make/Templates/ValueObject.stub'));
        $stub = str_replace('{bounded_context}', $context, $stub);
        $stub = str_replace('{value_object_name}', $valueObjectName, $stub);

        file_put_contents($valueObjectFile, $stub);
        $this->gitAdd($valueObjectFile);

        $this->info('ValueObject created' . PHP_EOL);

        $this->createValueObjectTest($context, $valueObjectName);

        return $valueObjectName;
    }

    public function createValueObjectTest(string $context, string $valueObjectName): void
    {
        $this->comment('Creating ValueObject Test');
        $valueObjectTestFile = base_path() . '/flyt-core/' . $context . '/Tests/Domain/ValueObjects/' . $valueObjectName . 'Test.php';

        $stub = file_get_contents(app_path('Console/Commands/Make/Templates/TestValueObject.stub'));
        $stub = str_replace('{bounded_context}', $context, $stub);
        $stub = str_replace('{camel_case_value_object_name}', camel_case($valueObjectName), $stub);
        $stub = str_replace('{value_object_name}', $valueObjectName, $stub);

        file_put_contents($valueObjectTestFile, $stub);
        $this->gitAdd($valueObjectTestFile);

        $this->info('ValueObject Test created');
    }
}