<?php

namespace Furrina\Storage;

class DirectoryStorage
{
    private $directories = [];

    public function __construct(array $definition = [])
    {
        $this->directories = [];
        foreach ($definition as $key => $value) {
            $this->directories[$key] = new Directory(
                new StoredFile($value[0], $value[1], $value[2]),
                $value[3] ?? []
            );
        }
    }

    public function getDirectoriesCount(): int
    {
        return count($this->directories);
    }

    public function getDirectory(int $key = 0): Directory
    {
        if (!isset($this->directories[$key])) {
            throw new \InvalidArgumentException("Directory with $key not found");
        }
        return $this->directories[$key];
    }

    public function addDirectory(Directory $directory): self
    {
        $this->directories[] = $directory;
        return $this;
    }

    /**
     * @return Directory[]
     */
    public function getDirectories(): array
    {
        return $this->directories;
    }
}
