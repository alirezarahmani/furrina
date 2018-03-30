<?php

namespace Furrina\Storage;

class StoredFile
{

    private $id;
    private $namespace;
    private $serversIds;

    public function __construct(int $id, int $namespace, array $serversIds)
    {
        $this->id = $id;
        $this->namespace = $namespace;
        $this->serversIds = $serversIds;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNamespace(): int
    {
        return $this->namespace;
    }

    public function getServersIds(): array
    {
        return $this->serversIds;
    }
}
