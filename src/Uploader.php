<?php

namespace Vista\Upload;

interface Uploader
{
    /**
     * @param array<string> $types, e.g. ['image/jpeg', 'image/png']
     */
    public function setAllowedMimeTypes(array $types): self;

    /**
     * @param int $size File size in bytes
     */
    public function setMaxFileSize(int $size): self;

    /**
     * @param string $path Absolute path to the upload directory.
     */
    public function setUploadDir(string $path): self;

    public function allowOverwrite(bool $enable): self;

    public function setFilename(string $name): self;

    public function setFilenamePrefix(string $prefix): self;

    public function setFilenameSuffix(string $suffix): self;

    public function validate(): bool;

    public function upload(): bool;

    /**
     * @return array<string, mixed>
     */
    public function getResults(): array;

    /**
     * @return array<string>
     */
    public function getErrors(): array;

    public function deleteUploadedFile(string $filePath): bool;
}
