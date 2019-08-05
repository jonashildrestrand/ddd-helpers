<?php

namespace App\Console\Commands\Make\Traits;

trait CreateBoundedContext
{
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function createBoundedContext(): void
    {
        $context = $this->ask('What should your bounded-context be called');

        while (file_exists((base_path() . '/flyt-core/' . $context))) {
            $context = $this->ask('The bounded-context "' . $context . '" already exists! Please try again');
        }

        $path = (base_path() . '/flyt-core/' . $context);

        $this->createRoot($path);
        $this->populateRoot($path);
        $this->populateDomain($path);
        $this->populateInfrastructure($path);
        $this->populateTests($path);
        $this->createTestCases($context);
        $this->createProviders($context);
    }

    private function createRoot($path)
    {
        $this->comment('Creating Bounded Context');
        if (!mkdir($path)) {
            die('Failed to create folders');
        }
    }

    private function populateRoot($path)
    {
        $this->comment('Creating Bounded Context Root Folders');
        if (!mkdir($path . '/Domain')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Infrastructure')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Tests')) {
            die('Failed to create folders');
        }
    }

    private function populateDomain($path)
    {
        $this->comment('Creating Domain Folders');
        if (!mkdir($path . '/Domain/Contracts')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Domain/Entities')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Domain/Events')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Domain/Exceptions')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Domain/Services')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Domain/ValueObjects')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Domain/ValueObjects/Traits')) {
            die('Failed to create folders');
        }
    }

    private function populateInfrastructure($path)
    {
        $this->comment('Creating Infrastructure Folders');
        if (!mkdir($path . '/Infrastructure/Listeners')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Infrastructure/Providers')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Infrastructure/Repositories')) {
            die('Failed to create folders');
        }
    }

    private function populateTests($path)
    {
        $this->comment('Creating Test Root Folders');
        if (!mkdir($path . '/Tests/Domain')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Tests/Infrastructure')) {
            die('Failed to create folders');
        }

        $this->comment('Creating Domain Test Folders');
        if (!mkdir($path . '/Tests/Domain/Contracts')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Tests/Domain/Entities')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Tests/Domain/Events')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Tests/Domain/Exceptions')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Tests/Domain/Services')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Tests/Domain/ValueObjects')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Tests/Domain/ValueObjects/Traits')) {
            die('Failed to create folders');
        }

        $this->comment('Creating Infrastructure Test Folders');
        if (!mkdir($path . '/Tests/Infrastructure/Listeners')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Tests/Infrastructure/Providers')) {
            die('Failed to create folders');
        }

        if (!mkdir($path . '/Tests/Infrastructure/Repositories')) {
            die('Failed to create folders');
        }
    }

    private function createTestCases(string $context): void
    {
        $this->comment('Creating Test Cases');

        $unit = file_get_contents(app_path('Console/Commands/Make/Templates/UnitTestCase.stub'));
        $unit = str_replace('{bounded_context}', $context, $unit);
        $unitFile = base_path() . '/flyt-core/' . $context . '/Tests/UnitTestCase.php';
        file_put_contents($unitFile, $unit);
        $this->gitAdd($unitFile);

        $integration = file_get_contents(app_path('Console/Commands/Make/Templates/IntegrationTestCase.stub'));
        $integration = str_replace('{bounded_context}', $context, $integration);
        $integrationFile = base_path() . '/flyt-core/' . $context . '/Tests/IntegrationTestCase.php';
        file_put_contents($integrationFile, $integration);
        $this->gitAdd($integrationFile);
    }

    private function createProviders(string $context): void
    {
        $this->comment('Creating Providers');

        $event = file_get_contents(app_path('Console/Commands/Make/Templates/EventServiceProvider.stub'));
        $event = str_replace('{bounded_context}', $context, $event);
        $eventFile = base_path() . '/flyt-core/' . $context . '/Infrastructure/Providers/EventServiceProvider.php';
        file_put_contents($eventFile, $event);
        $this->gitAdd($eventFile);

        $service = file_get_contents(app_path('Console/Commands/Make/Templates/ServiceProvider.stub'));
        $service = str_replace('{bounded_context}', $context, $service);
        $serviceFile = base_path() . '/flyt-core/' . $context . '/Infrastructure/Providers/ServiceProvider.php';
        file_put_contents($serviceFile, $service);
        $this->gitAdd($serviceFile);
    }
}