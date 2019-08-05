<?php

namespace App\Console\Commands\Make\Traits;

trait CreateContractAndRepository
{
    use CreateRepository;

    public function createContractAndRepository(string $context)
    {
        $context = $this->selectBoundedContext();
        $contractName = $this->createContract($context);
        $this->createRepository($context, $contractName);
    }
}