<?php

namespace Furrina\Storage;

class File extends FileBase
{

    private $name;
    private $size;

    public function __construct(StoredFile $storedFile, string $name, int $size, array $tags = [])
    {
        parent::__construct($storedFile, $tags);
        $this->name = $name;
        $this->size = $size;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getExtension(): string
    {
        return pathinfo($this->name)['extension'];
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
