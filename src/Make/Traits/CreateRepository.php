<?php

namespace App\Console\Commands\Make\Traits;

trait CreateRepository
{
    use ListPhpFiles, CreateContract;

    public function createRepository(string $context, string $contractName = null): string
    {
        if (!$contractName) {
            $contractName = $this->selectContract($context);
        }

        $this->comment('Creating Repositories');
        $repositoryName = $this->ask('Name of repository');
        $repositoryName = $this->enforceRepositoryNamingConvention($repositoryName);
        $repositoryFile = base_path() . '/flyt-core/' . $context . '/Infrastructure/Repositories/' . $repositoryName . '.php';

        while (file_exists(($repositoryFile))) {
            $repositoryName = $this->ask('The repository "' . $repositoryName . '" already exists! Please try again');
            $repositoryName = $this->enforceRepositoryNamingConvention($repositoryName);
            $repositoryFile = base_path() . '/flyt-core/' . $context . '/Infrastructure/Repositories/' . $repositoryName . '.php';
        }

        $stub = file_get_contents(app_path('Console/Commands/Make/Templates/Repository.stub'));
        $stub = str_replace('{bounded_context}', $context, $stub);
        $stub = str_replace('{contract_name}', $contractName, $stub);
        $stub = str_replace('{repository_name}', $repositoryName, $stub);

        file_put_contents($repositoryFile, $stub);
        $this->gitAdd($repositoryFile);

        $this->createRepositoryTest($context, $repositoryName);
        $this->addContractAndRepositoryToProvider($context, $contractName, $repositoryName);

        return $repositoryName;
    }

    private function selectContract(string $context): string
    {
        $path = base_path() . '/flyt-core/' . $context . '/Domain/Contracts/';
        $interfaces = $this->getFiles($path);

        if (empty($interfaces)) {
            $interfaceChoice = $this->choice('No interfaces found', [
                0 => 'Create new interface',
                1 => 'Exit'
            ]);

            if ($interfaceChoice === 'Create new interface') {
                return $this->createContract($context);
            } elseif ($interfaceChoice === 'Exit')
            {
                die();
            }
        }

        return $this->choice('Which interface should your repository implement', $this->getFiles($path));
    }

    private function enforceRepositoryNamingConvention(string $name): string
    {
        $name = str_replace('repository', 'Repository', $name);

        if (strpos($name, 'Repository') !== false) {
            return $name;
        } else {
            return $name . 'Repository';
        }
    }

    public function createRepositoryTest(string $context, string $repositoryName): void
    {
        $this->comment('Creating Repository Test');
        $repositoryTestFile = base_path() . '/flyt-core/' . $context . '/Tests/Infrastructure/Repositories/' . $repositoryName . 'Test.php';

        $stub = file_get_contents(app_path('Console/Commands/Make/Templates/TestRepository.stub'));
        $stub = str_replace('{bounded_context}', $context, $stub);
        $stub = str_replace('{camel_case_value_object_name}', camel_case($repositoryName), $stub);
        $stub = str_replace('{repository_name}', $repositoryName, $stub);

        file_put_contents($repositoryTestFile, $stub);
        $this->gitAdd($repositoryTestFile);

        $this->info('Repository Test created' . PHP_EOL);
    }

    public function addContractAndRepositoryToProvider(string $context, string $contractName, string $repositoryName): void
    {
        $this->comment('Adding Contract and Repository to Service Provider');

        $serviceFile = base_path() . '/flyt-core/' . $context . '/Infrastructure/Providers/ServiceProvider.php';
        $service = file_get_contents($serviceFile);

        if(strpos($service, $contractName) === false) {
            $needle = 'return [';
            $insertPosition = (strpos($service, $needle) + strlen($needle) + 1);
            $stub = file_get_contents(app_path('Console/Commands/Make/Templates/Partials/ProvideContract.stub'));
            $stub = str_replace('{contract_name}', $contractName, $stub);
            $service = substr_replace($service, $stub . PHP_EOL, $insertPosition, 0);
        }

        $contractDependency = 'use Flyt\\' . $context . '\\Domain\\Contracts\\' . $contractName . ';';
        $repositoryDependency = 'use Flyt\\' . $context . '\\Infrastructure\\Repositories\\' . $repositoryName . ';';
        $needle = 'BaseServiceProvider;';
        $insertPosition = (strpos($service, $needle) + strlen($needle));
        $service = substr_replace(
            $service,
            ((strpos($service, $contractDependency) !== false) ? '' : (PHP_EOL . $contractDependency)) . ((strpos($service, $repositoryDependency) !== false) ? '' : (PHP_EOL . $repositoryDependency)),
            $insertPosition,
            0
        );

        $needle = 'function registerRepositories()';
        $insertPosition = (strpos($service, $needle) + strlen($needle) + 7);
        $stub = file_get_contents(app_path('Console/Commands/Make/Templates/Partials/AppBindContract.stub'));
        $stub = str_replace('{bounded_context}', $context, $stub);
        $stub = str_replace('{contract_name}', $contractName, $stub);
        $stub = str_replace('{repository_name}', $repositoryName, $stub);
        $service = substr_replace($service, $stub, $insertPosition, 0);

        file_put_contents($serviceFile, $service);

        $this->info('Contract and repository added to Service Provider' . PHP_EOL);
    }
}