<?php

namespace App\Console\Commands\Make\Traits;

use Symfony\Component\Process\Process;

trait GitAdd
{
    public function gitAdd(string $file)
    {
        $gitAddFile = new Process('git add ' . $file);
        $gitAddFile->run();
    }
}