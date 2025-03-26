<?php

namespace Vista\Upload;

use Vista\Upload\Info\Factory as InfoFactory;
use Vista\Upload\Info\FileInfoFactory;
use Vista\Upload\Info\Info;
use Vista\Upload\System\Factory as SystemFactory;
use Vista\Upload\System\FileSystemFactory;
use Vista\Upload\System\System;

class FileUploader implements Uploader
{
    private System $fileSystem;
    private Info $fileInfo;
    private array $allowedTypes = [];
    private int $maxSize = 0;
    private string $uploadDir = '';
    private bool $allowOverwrite = false;
    private string $filenamePrefix = '';
    private string $filenameSuffix = '';
    private string $filename = '';
    private array $results = [];
    private array $errors = [];

    /**
     * @param string $name The name of the file input field.
     */
    public function __construct(
        string $name = '',
        SystemFactory $systemFactory = new FileSystemFactory(),
        InfoFactory $infoFactory = new FileInfoFactory()
    ) {
        $this->fileSystem = $systemFactory->create();
        $this->fileInfo = $infoFactory->create($name);
    }

    /**
     * @param array<string> $types, e.g. ['image/jpeg', 'image/png']
     */
    public function setAllowedMimeTypes(array $types): self
    {
        $this->allowedTypes = $types;

        return $this;
    }

    /**
     * @param int $size File size in bytes
     */
    public function setMaxFileSize(int $size): self
    {
        $this->maxSize = $size;

        return $this;
    }

    /**
     * @param string $path Absolute path to the upload directory.
     */
    public function setUploadDir(string $path): self
    {
        $this->uploadDir = $path;

        return $this;
    }

    public function allowOverwrite(bool $enable = true): self
    {
        $this->allowOverwrite = $enable;

        return $this;
    }

    public function setFilename(string $name): self
    {
        $this->filename = $name;

        return $this;
    }

    public function setFilenamePrefix(string $prefix): self
    {
        $this->filenamePrefix = $prefix;

        return $this;
    }

    public function setFilenameSuffix(string $suffix): self
    {
        $this->filenameSuffix = $suffix;

        return $this;
    }

    public function validate(): bool
    {
        return $this->validateNoErrors()
            && $this->validateMimeTypes()
            && $this->validateSize()
            && $this->validateUploadDir();
    }

    public function upload(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        return $this->uploadFile();
    }

    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @return array<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getOriginalFilename(): string
    {
        return $this->fileInfo->getFileName();
    }

    public function getOriginalExtension(): string
    {
        return $this->fileInfo->getExtension();
    }

    public function delete(string $filePath): bool
    {
        if ($this->fileSystem->exists($filePath)) {
            return $this->fileSystem->delete($filePath);
        }

        return false;
    }

    private function uploadFile(): bool
    {
        [$filename, $destination] = $this->generateFileNameAndDestination();

        if (!$this->moveUploadedFile($this->fileInfo->getTmpName(), $destination)) {
            return false;
        }

        $this->addResult($filename, $destination);

        return true;
    }

    private function moveUploadedFile(string $source, string $destination): bool
    {
        return $this->validateFileNotExists($destination)
            && $this->moveFileAndValidate($source, $destination);
    }

    private function addResult(string $filename, string $destination): void
    {
        $this->results[] = [
            'filename' => $filename,
            'destination' => $destination,
        ];
    }

    private function validateNoErrors(): bool
    {
        $error = $this->fileInfo->getError();

        if ($error !== UPLOAD_ERR_OK) {
            $this->errors[] = 'Upload error: ' . $error;

            return false;
        }

        return true;
    }

    private function validateMimeTypes(): bool
    {
        $mimeType = $this->fileInfo->getMimeType();

        if (!empty($this->allowedTypes) && !in_array($mimeType, $this->allowedTypes)) {
            $this->errors[] = 'File type not allowed: ' . $mimeType;

            return false;
        }

        return true;
    }

    private function validateSize(): bool
    {
        $size = $this->fileInfo->getSize();

        if ($this->maxSize > 0 && $size > $this->maxSize) {
            $this->errors[] = 'File size exceeds limit: ' . $size;

            return false;
        }

        return true;
    }

    private function validateUploadDir(): bool
    {
        if ($this->uploadDir === '') {
            $this->errors[] = 'Upload directory not set';

            return false;
        }

        return true;
    }

    /**
     * @return array{string, string}
     */
    private function generateFilenameAndDestination(): array
    {
        $filename = $this->generateFileName();
        $destination = $this->uploadDir . '/' . $filename;

        return [$filename, $destination];
    }

    /**
     * @return string
     */
    private function generateFileName(): string
    {
        return $this->filenamePrefix . ($this->filename ?: $this->fileInfo->getFileName()) . $this->filenameSuffix;
    }

    private function validateFileNotExists(string $destination): bool
    {
        if ($this->fileSystem->exists($destination) && !$this->allowOverwrite) {
            $this->errors[] = 'File already exists: ' . $destination;

            return false;
        }

        return true;
    }

    private function moveFileAndValidate(string $source, string $destination): bool
    {
        if (!$this->fileSystem->moveUploadedFile($source, $destination)) {
            $this->errors[] = 'Failed to move file: ' . $destination;

            return false;
        }

        return true;
    }
}
