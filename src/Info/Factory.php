<?php

namespace Vista\Upload\Info;

interface Factory
{
    /**
     * @param string $name The name of the file input field
     */
    public function create(string $name): Info;
}
