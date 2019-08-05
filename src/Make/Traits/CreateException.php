<?php

namespace App\Console\Commands\Make\Traits;

trait CreateException
{
    public function createException(string $context): string
    {
        $this->comment('Creating Exception');
        $exceptionName = $this->ask('Name of exception');
        $exceptionFile = base_path() . '/flyt-core/' . $context . '/Domain/Exceptions/' . $exceptionName . '.php';

        while (file_exists(($exceptionFile))) {
            $exceptionName = $this->ask('The exception "' . $exceptionName . '" already exists! Please try again');
            $exceptionFile = base_path() . '/flyt-core/' . $context . '/Domain/Exceptions/' . $exceptionName . '.php';
        }

        $stub = file_get_contents(app_path('Console/Commands/Make/Templates/Exception.stub'));
        $stub = str_replace('{bounded_context}', $context, $stub);
        $stub = str_replace('{exception_name}', $exceptionName, $stub);

        file_put_contents($exceptionFile, $stub);
        $this->gitAdd($exceptionFile);

        $this->info('Exception created' . PHP_EOL);

        return $exceptionName;
    }
}