<?php

namespace App\Console\Commands\Make\Traits;

use DirectoryIterator;

trait SelectContext
{

    public function selectBoundedContext(string $message = 'Choose bounded context'): string
    {
        $dirs = array();

        foreach (new DirectoryIterator((base_path() .'/flyt-core/')) as $file) {
            if ($file->isDir() && !$file->isDot()) {
                $dirs[] = $file->getFilename();
            }
        }

        return $context = $this->choice($message, $dirs);
    }
}