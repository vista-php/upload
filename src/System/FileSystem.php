<?php

namespace Vista\Upload\System;

class FileSystem implements System
{
    public function exists(string $filePath): bool
    {
        return file_exists($filePath);
    }

    public function delete(string $filePath): bool
    {
        return unlink($filePath);
    }

    public function moveUploadedFile(string $source, string $destination): bool
    {
        return move_uploaded_file($source, $destination);
    }
}
