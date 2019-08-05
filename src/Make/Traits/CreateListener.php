<?php

namespace App\Console\Commands\Make\Traits;

trait CreateListener
{
    public function createListener(string $context): string
    {
        $this->comment('Creating Listener');

        $nameingMethod = $this->choice('Use custom name for for listener?', [
            0 => 'Yes',
            1 => 'No, autogenerate',
        ]);
        if($nameingMethod === 'Yes'){
            $listenerName = $this->ask('(1/3) Name of listener');
            $listenerName = $this->enforceListenerNamingConvention($listenerName);
        }


        /*
        $listenerFile = base_path() . '/flyt-core/' . $context . '/Infrastructure/Listeners/' . $listenerName . '.php';

        while (file_exists(($listenerFile))) {
            $listenerName = $this->ask('The listener "' . $listenerName . '" already exists! Please try again');
            $listenerFile = base_path() . '/flyt-core/' . $context . '/Infrastructure/Listeners/' . $listenerName . '.php';
        }
        */
        $eventContext = $this->selectBoundedContext('(2/3) Choose the bounded context that holds the event you want to listen to');
        $eventPath = base_path() . '/flyt-core/' . $eventContext . '/Domain/Events/';

        while(empty($this->getFiles($eventPath))) {
            $interfaceChoice = $this->choice('No events found', [
                0 => 'Try another bounded context',
                1 => 'Create new event',
                2 => 'Exit'
            ]);

            if ($interfaceChoice === 'Try another bounded context') {
                $eventContext = $this->selectBoundedContext();
                $eventPath = base_path() . '/flyt-core/' . $eventContext . '/Domain/Events/';
            } elseif ($interfaceChoice === 'Create new event') {
                return $this->createEvent($eventContext);
            } elseif ($interfaceChoice === 'Exit') {
                die();
            }
        }

        $eventName = $this->choice('(3/3) Which event do you want to listen to', $this->getFiles($eventPath));

        if($nameingMethod !== 'Yes') {
            $listenerName = $this->enforceListenerNamingConvention($eventName);
        }
        $listenerFile = base_path() . '/flyt-core/' . $context . '/Infrastructure/Listeners/' . $listenerName . '.php';

        while (file_exists(($listenerFile))) {
            $listenerName = $this->ask('The listener "' . $listenerName . '" already exists! Please try again');
            $listenerFile = base_path() . '/flyt-core/' . $context . '/Infrastructure/Listeners/' . $listenerName . '.php';
        }
        $stub = file_get_contents(app_path('Console/Commands/Make/Templates/Listener.stub'));
        $stub = str_replace('{bounded_context}', $context, $stub);
        $stub = str_replace('{listener_name}', $listenerName, $stub);
        $stub = str_replace('{event_bounded_context}', $eventContext, $stub);
        $stub = str_replace('{event_name}', $eventName, $stub);


        file_put_contents($listenerFile, $stub);
        $this->gitAdd($listenerFile);

        $this->info('Listener created' . PHP_EOL);

        $this->createListenerTest($context, $listenerName);
        $this->updateEventProvider($context, $eventContext, $listenerName, $eventName);

        return $listenerName;
    }

    private function updateEventProvider(string $context, string $eventContext, string $listenerName, string $eventName)
    {
        $this->comment('Adding Contract and Repository to Service Provider');

        $serviceFile = base_path() . '/flyt-core/' . $context . '/Infrastructure/Providers/EventServiceProvider.php';
        $service = file_get_contents($serviceFile);

        if(strpos($service, $listenerName) === false) {
            $needle = '$listen = [';
            $insertPosition = (strpos($service, $needle) + strlen($needle) + 1);
            $stub = file_get_contents(app_path('Console/Commands/Make/Templates/Partials/EventBindListener.stub'));
            $stub = str_replace('{listener_name}', $listenerName, $stub);
            $stub = str_replace('{event_name}', $eventName, $stub);
            $service = substr_replace($service, $stub . PHP_EOL, $insertPosition, 0);
        }

        $contractDependency = 'use Flyt\\' . $eventContext . '\\Domain\\Events\\' . $eventName . ';';
        $repositoryDependency = 'use Flyt\\' . $context . '\\Infrastructure\\Listeners\\' . $listenerName . ';';
        $needle = 'ServiceProvider;';
        $insertPosition = (strpos($service, $needle) + strlen($needle));
        $service = substr_replace(
            $service,
            ((strpos($service, $contractDependency) !== false) ? '' : (PHP_EOL . $contractDependency)) . ((strpos($service, $repositoryDependency) !== false) ? '' : (PHP_EOL . $repositoryDependency)),
            $insertPosition,
            0
        );

        file_put_contents($serviceFile, $service);

        $this->info('Contract and repository added to Service Provider' . PHP_EOL);
    }

    public function createListenerTest(string $context, string $listenerName): void
    {
        $this->comment('Creating Listener Test');
        $listenerTestFile = base_path() . '/flyt-core/' . $context . '/Tests/Infrastructure/Listeners/' . $listenerName . 'Test.php';

        $stub = file_get_contents(app_path('Console/Commands/Make/Templates/TestListener.stub'));
        $stub = str_replace('{bounded_context}', $context, $stub);
        $stub = str_replace('{listener_name}', $listenerName, $stub);

        file_put_contents($listenerTestFile, $stub);
        $this->gitAdd($listenerTestFile);

        $this->info('Listener Test created');
    }


    private function enforceListenerNamingConvention(string $name): string
    {
        $name = str_replace('listener', 'Listener', $name);

        if (strpos($name, 'Listener') !== false) {
            return $name;
        } else {
            return $name . 'Listener';
        }
    }
}