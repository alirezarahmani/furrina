<?php

namespace Furrina\Storage;

class FileNotFoundException extends \Exception
{

    private $id;
    private $namespace;

    public function __construct(string $namespace, int $id)
    {
        parent::__construct("File $id not found in namespace $namespace");
        $this->id = $namespace;
        $this->namespace = $namespace;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }
}
