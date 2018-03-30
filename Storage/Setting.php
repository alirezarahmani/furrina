<?php

namespace Furrina\Storage;

use Symfony\Component\Yaml\Yaml;

class Setting
{
    private static $instance;

    public function get(): ?array
    {
       return Yaml::parseFile(__DIR__.'../Config/setting.yml');
    }

    public final static function getConfig(): ?array
    {
        if (!self::$instance || !is_a(self::$instance, self::class)) {
            self::$instance = new self();
        }
        return $data = self::$instance->get();
    }
}