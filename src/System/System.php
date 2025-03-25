<?php

namespace Vista\Upload\System;

interface System
{
    public function exists(string $filePath): bool;

    public function delete(string $filePath): bool;

    public function moveUploadedFile(string $source, string $destination): bool;
}
