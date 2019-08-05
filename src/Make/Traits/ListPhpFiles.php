<?php

namespace App\Console\Commands\Make\Traits;

trait ListPhpFiles
{
    public function getFiles(string $path, string $fileType = '*.php'): array
    {
        $files = array();

        foreach (glob($path . $fileType) as $file) {
            $filename = explode('/', $file);
            $size = sizeof($filename);
            $filename = explode('.', $filename[$size-1]);
            $files[] = $filename[0];
        }

        return $files;
    }
}