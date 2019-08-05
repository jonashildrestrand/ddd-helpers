<?php

namespace App\Console\Commands\Make\Traits;

trait CreateContract
{
    public function createContract(string $context): string
    {
        $this->comment('Creating Contract');
        $contractName = $this->ask('Name of contract');
        $contractName = $this->enforceInterfaceNamingConvention($contractName);
        $contractFile = base_path() . '/flyt-core/' . $context . '/Domain/Contracts/' . $contractName . '.php';

        while (file_exists(($contractFile))) {
            $contractName = $this->ask('The contract "' . $contractName . '" already exists! Please try again');
            $contractName = $this->enforceInterfaceNamingConvention($contractName);
            $contractFile = base_path() . '/flyt-core/' . $context . '/Domain/Contracts/' . $contractName . '.php';
        }

        $stub = file_get_contents(app_path('Console/Commands/Make/Templates/Contract.stub'));
        $stub = str_replace('{bounded_context}', $context, $stub);
        $stub = str_replace('{contract_name}', $contractName, $stub);

        file_put_contents($contractFile, $stub);
        $this->gitAdd($contractFile);

        return $contractName;
    }

    private function enforceInterfaceNamingConvention(string $name): string
    {
        $name = str_replace('interface', 'Interface', $name);

        if (strpos($name, 'Interface') !== false) {
            return $name;
        } else {
            return $name . 'Interface';
        }
    }
}