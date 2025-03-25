<?php

namespace Vista\Upload\System;

readonly class FileSystemFactory implements Factory
{
    public function __construct(
        private ?System $system = null
    ) {
    }

    public function create(): FileSystem
    {
        if ($this->system !== null) {
            return $this->system;
        }

        return new FileSystem();
    }
}
