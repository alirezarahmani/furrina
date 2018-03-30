<?php

namespace Furrina\Storage;

class Image extends FileBase
{

    private $width;
    private $height;
    private $format;

    public function __construct(StoredFile $storedFile, int $width, int $height, string $format = ImageDefinition::FORMAT_JPG, array $tags = [])
    {
        parent::__construct($storedFile, $tags);
        $this->width = $width;
        $this->height = $height;
        $this->format = $format;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
