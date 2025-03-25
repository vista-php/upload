<?php

namespace Vista\Upload\Info;

readonly class FileInfoFactory implements Factory
{
    public function __construct(
        private ?Info $info = null
    ) {
    }

    /**
     * @param string $name The name of the file input field.
     */
    public function create(string $name): Info
    {
        if ($this->info !== null) {
            return $this->info;
        }

        return new FileInfo($_FILES[$name]);
    }
}
