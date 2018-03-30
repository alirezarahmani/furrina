<?php

namespace Furrina\Storage;

abstract class FileBase
{

    private $storedFile;
    private $tags;

    public function __construct(StoredFile $storedFile, array $tags = [])
    {
        $this->storedFile = $storedFile;
        $this->tags = $tags;
    }

    public function getStoredFile(): StoredFile
    {
        return $this->storedFile;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getTag(string $key): ?string
    {
        return $this->tags[$key] ?? null;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function setTag(string $key, string $value): self
    {
        $this->tags[$key] = $value;
        return $this;
    }

    public function hasTag(string $key): bool
    {
        return isset($this->tags[$key]);
    }
}
