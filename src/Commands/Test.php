<?php

namespace DDDHelpers\Commands;

use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ddd:test';

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
    }
}