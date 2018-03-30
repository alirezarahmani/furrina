<?php

namespace Furrina\Storage;

class ImageStorage
{
    private $definition;

    public function __construct(array $definition = [])
    {
        $this->definition = [];
        foreach ($definition as $key => $value) {
            $this->definition[$key] = new Image(
                new StoredFile($value[0], $value[1], $value[5]),
                $value[2],
                $value[3],
                $value[4] ?? ImageDefinition::FORMAT_JPG,
                $value[6] ?? []
            );
        }
    }

    public function getSizesCount(): int
    {
        return count($this->definition);
    }

    public function hasSize(string $key): bool
    {
        return isset($this->definition[$key]);
    }

    public function getImage(string $key = 'default'): Image
    {
        if (!isset($this->definition[$key])) {
            throw new \InvalidArgumentException("image with size $key not found");
        }
        return $this->definition[$key];
    }

    public function addImage(string $key, Image $image): self
    {
        $this->definition[$key] = $image;
        return $this;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->definition;
    }
}
