<?php

namespace Furrina\Storage;

interface StorageProviderInterface
{

    public function save(int $id, string $namespace, string $content): array;

    public function saveZip(int $id, string $namespace, string $content): array;

    public function delete(int $id, string $namespace, array $serverIds);

    public function get(int $id, string $namespace, array $serverIds): ?string;
}
