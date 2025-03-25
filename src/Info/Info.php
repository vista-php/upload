<?php

namespace Vista\Upload\Info;

interface Info
{
    public function getFilename(): string;

    public function getMimeType(): string;

    public function getSize(): int;

    public function getTmpName(): string;

    public function getError(): int;
}
