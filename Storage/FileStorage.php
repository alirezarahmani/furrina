<?php

namespace Furrina\Storage;

class FileStorage
{
    private $files = [];

    public function __construct(array $definition = [])
    {
        $this->files = [];
        foreach ($definition as $key => $value) {
            $this->files[$key] = new File(
                new StoredFile($value[0], $value[1], $value[4]),
                $value[2],
                $value[3],
                $value[5] ?? []
            );
        }
    }

    public function getFilesCount(): int
    {
        return count($this->files);
    }

    public function getFile(int $key = 0): File
    {
        if (!isset($this->files[$key])) {
            throw new \InvalidArgumentException("File with $key not found");
        }
        return $this->files[$key];
    }

    public function addFile(File $file): self
    {
        $this->files[] = $file;
        return $this;
    }

    /**
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
