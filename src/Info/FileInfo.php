<?php

namespace Vista\Upload\Info;

readonly class FileInfo implements Info
{
    /**
     * @param array<string, mixed> $file The $_FILES array.
     */
    public function __construct(
        private array $file
    ) {
    }

    public function getFilename(): string
    {
        return $this->file['name'] ?? '';
    }

    public function getExtension(): string
    {
        return pathinfo($this->file['name'] ?? '', PATHINFO_EXTENSION);
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
