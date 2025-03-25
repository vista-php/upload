<?php

namespace Vista\Upload\Info;

class FileInfo implements Info
{
    /**
     * @param array<string, mixed> $file The $_FILES array.
     */
    public function __construct(
        private readonly array $file
    ) {
    }

    public function getFilename(): string
    {
        return $this->file['name'] ?? '';
    }

    public function getMimeType(): string
    {
        if (empty($this->file['tmp_name'])) {
            return $this->file['type'] ?? '';
        }

        if (!file_exists($this->file['tmp_name'])) {
            return $this->file['type'] ?? '';
        }

        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $this->file['tmp_name'] ?? '');
        finfo_close($fileInfo);

        return $mimeType ?: $this->file['type'] ?? '';
    }

    public function getSize(): int
    {
        return $this->file['size'] ?? 0;
    }

    public function getTmpName(): string
    {
        return $this->file['tmp_name'] ?? '';
    }

    public function getError(): int
    {
        return $this->file['error'] ?? 0;
    }
}
