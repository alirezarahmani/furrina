<?php

namespace Furrina\Storage;

class StorageException extends \Exception
{

    private $id;
    private $namespace;

    public function __construct(string $namespace, int $id, string $message)
    {
        parent::__construct("Error for file $id in $namespace namespace: $message");
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
