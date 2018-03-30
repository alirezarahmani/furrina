<?php

namespace Furrina\Storage;

use Memcached;
use Brisk\ORM\Cache\CacheStorageInterface;
use Brisk\ORM\Setting;
use InvalidArgumentException;

class MemcachedCache implements CacheStorageInterface
{
    /**
     * @var Memcached
     */
    private $memcached;
    
    public function get(string $key)
    {
        $value = $this->executeCommand(
            'get',
            [$key]
        );
        if (!$value) {
            if ($this->getMemcached()->getResultCode() == Memcached::RES_NOTFOUND) {
                return null;
            }
            return false;
        }
        return $value;
    }

    public function setValue(string $key, $value)
    {
        $this->executeCommand(
            'set',
            [$key, $value, 0]
        );
    }

    public function getStats(): array
    {
        $stats = [];
        $hosts = Setting::getConfig()['memcached']['hosts'];
        foreach ($hosts as $host) {
            preg_match('/(?P<protocol>\w+):\/\/(?P<host>[0-9a-z._]*):(?P<port>\d+)/', $host, $matches);
            $memcached = new Memcached();
            $memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, false);
            $memcached->addServer($matches['host'], $matches['port']);
            $stats = array_merge($stats, $memcached->getStats());
            $memcached = null;
        }
        return $stats;
    }

    public function inc(string $key): int
    {
        
        $val = $this->executeCommand(
            'increment',
            [$key]
        );
        if ($this->getMemcached()->getResultCode() == Memcached::RES_NOTFOUND) {
            $this->setValue($key, 1);
            return 1;
        }
        return $val;
    }

    public function increment(string $key, int $by = 1): int
    {
        $val = $this->executeCommand(
            'increment',
            [$key, $by]
        );
        if ($this->getMemcached()->getResultCode() == Memcached::RES_NOTFOUND) {
            $this->setValue($key, $by);
            return $by;
        }
        return $val;
    }

    public function incExpire(string $key, int $ttl): int
    {
        $val = $this->executeCommand(
            'increment',
            [$key]
        );
        if ($this->getMemcached()->getResultCode() == Memcached::RES_NOTFOUND) {
            $this->setExpire($key, 1, $ttl);
            return 1;
        }
        return $val;
    }

    public function setExpire(string $key, $value, int $ttl)
    {
        if ($ttl > 2592000) {
            throw new InvalidArgumentException("TTL too big: $ttl");
        }
        $this->executeCommand(
            'set',
            [$key, $value, $ttl]
        );
    }

    public function dec(string $key): int
    {
        
        $val = $this->executeCommand(
            'dec',
            [$key]
        );
        if ($this->getMemcached()->getResultCode() == Memcached::RES_NOTFOUND) {
            $this->setValue($key, -1);
            return -1;
        }
        return $val;
    }

    public function decrement(string $key, int $by = 1): int
    {
        $val = $this->executeCommand(
            'decrement',
            [$key, $by]
        );
        if ($this->getMemcached()->getResultCode() == Memcached::RES_NOTFOUND) {
            $this->setValue($key, $by * -1);
            return $by * -1;
        }
        return $val;
    }

    public function set(string $key, $value, int $ttl)
    {
        if ($ttl > 2592000) {
            throw new InvalidArgumentException("TTL too big: $ttl");
        }
        $this->executeCommand(
            'set',
            [$key, $value, $ttl]
        );
    }

    public function delete(string $key)
    {
        return $this->executeCommand(
            'delete',
            [$key]
        );
    }

    public function deleteMany(array $keys)
    {
        return $this->executeCommand(
            'deleteMulti',
            [$keys]
        );
    }

    public function lock(string $key, int $ttl): bool
    {
        $val = $this->executeCommand(
            'add',
            [$key, $ttl]
        );
        if ($val) {
            $this->setExpire($key, 1, $ttl);
        }
        return $val;
    }

    public function unlock(string $key)
    {
        $this->delete($key);
    }

    public function gets(array $keys): array 
    {
        $values = $this->executeCommand(
            'getMulti',
            [$keys]
        );
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $values[$key] ?? null;
        }
        return $results;
    }

    public function clear()
    {
        $hosts = Setting::getConfig()['memcached']['hosts'];
        foreach ($hosts as $host) {
            preg_match('/(?P<protocol>\w+):\/\/(?P<host>[0-9a-z._]*):(?P<port>\d+)/', $host, $matches);
            $memcached = new Memcached();
            $memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, false);
            $memcached->addServer($matches['host'], $matches['port']);
            $memcached->flush();
            $memcached = null;
        }
    }

    private function executeCommand(string $command, array $args = [])
    {
        $memcached = $this->getMemcached();

        $value = call_user_func_array([$memcached, $command], $args);
        $resultCode = $memcached->getResultCode();
        if ($resultCode != Memcached::RES_SUCCESS) {
            if ($resultCode != Memcached::RES_NOTFOUND && $resultCode != Memcached::RES_NOTSTORED) {
                throw new \Exception("Invalid response from memcached: " . $memcached->getResultMessage());
            }
        }
        return $value;
    }

    private function getMemcached(): Memcached
    {
        if ($this->memcached === null) {
            $settings = Setting::getConfig()['memcached'];
            preg_match('/(?P<protocol>\w+):\/\/(?P<host>[0-9a-z._]*):(?P<port>\d+)/', $settings['mcrouter'], $matches);
            $this->memcached = new Memcached();
            $this->memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, false);
            $this->memcached->addServer($matches['host'], $matches['port']);
        }
        return $this->memcached;
    }

    public function iterateKeys(string $prefix = null): \Iterator
    {
        return null;
    }

    public function getFreeMemory(): int
    {
        return 0;
    }

    public function getAvailableMemory(): int
    {
        return 0;
    }
}
