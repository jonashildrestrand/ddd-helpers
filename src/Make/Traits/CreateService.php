<?php

namespace App\Console\Commands\Make\Traits;

trait CreateService
{
    public function createService(string $context): string
    {
        $this->comment('Creating Service');
        $serviceName = $this->ask('Name of service');
        $serviceName = $this->enforceServiceNamingConvention($serviceName);
        $serviceFile = base_path() . '/flyt-core/' . $context . '/Domain/Services/' . $serviceName . '.php';

        while (file_exists(($serviceFile))) {
            $serviceName = $this->ask('The service "' . $serviceName . '" already exists! Please try again');
            $serviceFile = base_path() . '/flyt-core/' . $context . '/Domain/Services/' . $serviceName . '.php';
        }

        $stub = file_get_contents(app_path('Console/Commands/Make/Templates/Service.stub'));
        $stub = str_replace('{bounded_context}', $context, $stub);
        $stub = str_replace('{service_name}', $serviceName, $stub);

        file_put_contents($serviceFile, $stub);
        $this->gitAdd($serviceFile);

        $this->info('Service created' . PHP_EOL);

        $this->createServiceTest($context, $serviceName);

        return $serviceName;
    }

    public function createServiceTest(string $context, string $serviceName): void
    {
        $this->comment('Creating Service Test');
        $serviceTestFile = base_path() . '/flyt-core/' . $context . '/Tests/Domain/Services/' . $serviceName . 'Test.php';

        $stub = file_get_contents(app_path('Console/Commands/Make/Templates/TestService.stub'));
        $stub = str_replace('{bounded_context}', $context, $stub);
        $stub = str_replace('{service_name}', $serviceName, $stub);

        file_put_contents($serviceTestFile, $stub);
        $this->gitAdd($serviceTestFile);

        $this->info('Service Test created');
    }


    private function enforceServiceNamingConvention(string $name): string
    {
        $name = str_replace('service', 'Service', $name);

        if (strpos($name, 'Service') !== false) {
            return $name;
        } else {
            return $name . 'Service';
        }
    }
}