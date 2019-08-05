<?php

namespace App\Console\Commands\Make;

use App\Console\Commands\Make\Traits\CreateBoundedContext;
use App\Console\Commands\Make\Traits\CreateContractAndRepository;
use App\Console\Commands\Make\Traits\CreateEntity;
use App\Console\Commands\Make\Traits\CreateEvent;
use App\Console\Commands\Make\Traits\CreateException;
use App\Console\Commands\Make\Traits\CreateListener;
use App\Console\Commands\Make\Traits\CreateService;
use App\Console\Commands\Make\Traits\CreateValueObject;
use App\Console\Commands\Make\Traits\GitAdd;
use App\Console\Commands\Make\Traits\SelectContext;
use Illuminate\Console\Command;

class Make extends Command
{
    use GitAdd;
    use CreateBoundedContext;
    use CreateContractAndRepository;
    use SelectContext;
    use CreateEntity;
    use CreateValueObject;
    use CreateService;
    use CreateException;
    use CreateEvent;
    use CreateListener;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:ddd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create files or folder structure for DDD structure';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {
        $this->info('');
        $this->info(' +-------------------------------------+');
        $this->info(' |                                     |');
        $this->info(' |            DDD: Helpers!            |');
        $this->info(' |                                     |');
        $this->info(' +-------------------------------------+');

        $choice = $this->choice('What would you like to create', [
            0 => 'Bounded-Context',
            1 => 'Contract & Repository',
            2 => 'Contract',
            3 => 'Entity',
            4 => 'Event',
            5 => 'Exception',
            6 => 'Service',
            7 => 'ValueObject',
            8 => 'ValueObject Trait',
            9 => 'Listener',
            10 => 'Repository'
        ]);

        $display = ' |                                     |';
        $displayLength = (strlen($display) - 2);

        $title = $choice . ((strlen($choice) % 2 === 0) ? '!' : '');
        $titleLength = strlen($title);
        $display = substr_replace($display, $title, (round($displayLength/2) - floor($titleLength/2) + 1), $titleLength);

        $this->info(' +-------------------------------------+');
        $this->info(' |              Creating:              |');
        $this->info(' +-------------------------------------+');
        $this->info(' |                                     |');
        $this->info($display);
        $this->info(' |                                     |');
        $this->info(' +-------------------------------------+');

        switch($choice)
        {
            case 'Bounded-Context':
                $this->createBoundedContext();
                break;

            case 'Contract & Repository':
                $this->createContractAndRepository($this->selectBoundedContext('Choose the bounded context you want to create your Interface and Repository in'));
                break;

            case 'Interface':
                $this->createContract($this->selectBoundedContext('Choose the bounded context you want to create your Interface in'));
                break;

            case 'Entity':
                $this->createEntity($this->selectBoundedContext('Choose the bounded context you want to create your Entity in'));
                break;

            case 'Event':
                $this->createEvent($this->selectBoundedContext('Choose the bounded context you want to create your Event in'));
                break;

            case 'Exception':
                $this->createException($this->selectBoundedContext('Choose the bounded context you want to create your Exception in'));
                break;

            case 'Service':
                $this->createService($this->selectBoundedContext('Choose the bounded context you want to create your Service in'));
                break;

            case 'ValueObject':
                $this->createValueObject($this->selectBoundedContext('Choose the bounded context you want to create your ValueObject in'));
                break;

            case 'Trait':
                //$this->createTrait($this->selectBoundedContext());
                break;

            case 'Listener':
                $this->createListener($this->selectBoundedContext('Choose the bounded context you want to create your Event Listener in'));
                break;

            case 'Repository':
                $this->createRepository($this->selectBoundedContext('Choose the bounded context you want to create your Repository in'));
                break;
        }
    }
}