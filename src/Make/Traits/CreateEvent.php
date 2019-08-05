<?php

namespace App\Console\Commands\Make\Traits;

trait CreateEvent
{
    public function createEvent(string $context): string
    {
        $this->comment('Creating Event');
        $eventName = $this->ask('Name of event');
        $eventFile = base_path() . '/flyt-core/' . $context . '/Domain/Events/' . $eventName . '.php';

        while (file_exists(($eventFile))) {
            $eventName = $this->ask('The event "' . $eventName . '" already exists! Please try again');
            $eventFile = base_path() . '/flyt-core/' . $context . '/Domain/Events/' . $eventName . '.php';
        }

        $stub = file_get_contents(app_path('Console/Commands/Make/Templates/Event.stub'));
        $stub = str_replace('{bounded_context}', $context, $stub);
        $stub = str_replace('{event_name}', $eventName, $stub);

        file_put_contents($eventFile, $stub);
        $this->gitAdd($eventFile);

        $this->info('Event created' . PHP_EOL);

        return $eventName;
    }
}