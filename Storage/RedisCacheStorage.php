<?php

namespace Furrina\Storage;

use Brisk\ORM\Cache\CacheStorageInterface;
use Brisk\ORM\Setting;

class RedisCacheStorage implements CacheStorageInterface
{

    private $name;
    private $keyNamespace;
    private $redis;

    public function __construct(string $poolName, string $keyNamespace)
    {
        $this->name = $poolName;
        $this->keyNamespace = $keyNamespace;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getKeyNamespace(): string
    {
        return $this->keyNamespace;
    }

    public function incr(string $key): int
    {
        $args = func_get_args();
        $value = $this->executeCommand('incr', $args, true);
        if ($value === false) {
            $this->delete($key);
            $value = $this->executeCommand('incr', $args, true);
        }
        return $value;
    }

    public function incrBy(string $key, int $value): int
    {
        $args = func_get_args();
        $value = $this->executeCommand('incrBy', $args, true);
        if ($value === false) {
            $this->delete($key);
            $value = $this->executeCommand('incrBy', $args, true);
        }
        return $value;
    }

    public function psetex(string $key, int $ttl, $value): bool
    {
        return $this->executeCommand('psetex', [$key, $ttl, $value], is_numeric($value));
    }

    public function sScan(string $key, &$iterator, string $pattern = '', int $count = 0)
    {
        $values = $this->executeCommand('sScan', [$key, &$iterator, $pattern, $count]);
        return $values;
    }

    public function scan(&$iterator, string $pattern = '', int $count = 0)
    {
        return $this->executeCommand('scan', func_get_args());
    }

    public function zScan(string $key, &$iterator, string $pattern = '', int $count = 0)
    {
        list($iterator, $values) = $this->executeCommand('zScan', [$key, $iterator, $pattern, $count]);
        return $values;
    }

    public function hScan(string $key, &$iterator, string $pattern = '', int $count = 0)
    {
        return $this->executeCommand('hScan', func_get_args());
    }

    public function client(string $command, string $arg = '')
    {
        return $this->executeCommand('client', func_get_args());
    }

    public function close(): bool
    {
        return $this->executeCommand('close');
    }

    public function ping(): string
    {
        return $this->executeCommand('ping');
    }

    public function set(string $key, $value, int $ttl): bool
    {
        return $this->executeCommand('setex', [$key, $ttl, $value], is_numeric($value));
    }

    public function setnx(string $key, $value): bool
    {
        return $this->executeCommand('setnx', func_get_args(), is_numeric($value));
    }

    public function multi()
    {
        return $this->executeCommand('multi');
    }

    public function exec(): array
    {
        return $this->executeCommand('exec');
    }

    public function discard()
    {
        return $this->executeCommand('discard');
    }

    public function watch($key)
    {
        return $this->executeCommand('watch', func_get_args());
    }

    public function unwatch()
    {
        return $this->executeCommand('unwatch', func_get_args());
    }

    public function subscribe(array $channels, $callback)
    {
        return $this->executeCommand('subscribe', func_get_args());
    }

    public function psubscribe(array $patterns, $callback)
    {
        return $this->executeCommand('psubscribe', func_get_args());
    }

    public function publish(string $channel, string $message): int
    {
        return $this->executeCommand('publish', func_get_args());
    }

    public function exists(string $key): bool
    {
        return $this->executeCommand('exists', func_get_args());
    }

    public function incrByFloat(string $key, float $increment): float
    {
        return $this->executeCommand('incrByFloat', func_get_args(), true);
    }

    public function decr(string $key): int
    {
        $args = func_get_args();
        $value = $this->executeCommand('decr', $args, true);
        if ($value === false) {
            $this->delete($key);
            $value = $this->executeCommand('decr', $args, true);
        }
        return $value;
    }

    public function decrBy(string $key, int $value): int
    {
        $args = func_get_args();
        $value = $this->executeCommand('decrBy', $args, true);
        if ($value === false) {
            $this->delete($key);
            $value = $this->executeCommand('decrBy', $args, true);
        }
        return $value;
    }

    public function getMultiple(array $keys): array
    {
        return $this->executeCommand('getMultiple', func_get_args());
    }

    public function lPush(string $key, $value1, $value2 = null, $valueN = null)
    {
        return $this->executeCommand('lPush', func_get_args());
    }

    public function rPush(string $key, $value1, $value2 = null, $valueN = null)
    {
        return $this->executeCommand('rPush', func_get_args());
    }

    public function lPushx(string $key, $value)
    {
        return $this->executeCommand('lPushx', func_get_args());
    }

    public function rPushx(string $key, $value)
    {
        return $this->executeCommand('rPushx', func_get_args());
    }

    public function lPop(string $key)
    {
        return $this->executeCommand('lPop', func_get_args());
    }

    public function rPop(string $key)
    {
        return $this->executeCommand('rPop', func_get_args());
    }

    public function blPop(array $keys): array
    {
        return $this->executeCommand('blPop', func_get_args());
    }

    public function brPop(array $keys): array
    {
        return $this->executeCommand('brPop', func_get_args());
    }

    public function lLen(string $key)
    {
        return $this->executeCommand('lLen', func_get_args());
    }

    public function lSize(string $key)
    {
        return $this->executeCommand('lSize', func_get_args());
    }

    public function lIndex(string $key, int $index)
    {
        return $this->executeCommand('lIndex', func_get_args());
    }

    public function lGet(string $key, int $index)
    {
        return $this->executeCommand('lGet', func_get_args());
    }

    public function lSet(string $key, int $index, $value): bool
    {
        return $this->executeCommand('lSet', func_get_args());
    }

    public function lRange(string $key, int $start, int $end): array
    {
        return $this->executeCommand('lRange', func_get_args());
    }

    public function lGetRange(string $key, int $start, int $end): array
    {
        return $this->executeCommand('lGetRange', func_get_args());
    }

    public function lTrim(string $key, int $start, int $stop)
    {
        return $this->executeCommand('lTrim', func_get_args());
    }

    public function listTrim(string $key, int $start, int $stop)
    {
        return $this->executeCommand('listTrim', func_get_args());
    }

    public function lRem(string $key, $value, int $count)
    {
        return $this->executeCommand('lRem', func_get_args());
    }

    public function lRemove(string $key, $value, int $count)
    {
        return $this->executeCommand('lRemove', func_get_args());
    }

    public function lInsert(string $key, int $position, $pivot, $value): int
    {
        return $this->executeCommand('lInsert', func_get_args());
    }

    public function sAdd(string $key, $value1, $value2 = null, $valueN = null): int
    {
        return $this->executeCommand('sAdd', func_get_args());
    }

    public function sAddArray(string $key, array $values): bool
    {
        return $this->executeCommand('sAddArray', func_get_args());
    }

    public function sRem(string $key, $member1, $member2 = null, $memberN = null): int
    {
        return $this->executeCommand('sRem', func_get_args());
    }

    public function sRemove(string $key, $member1, $member2 = null, $memberN = null): int
    {
        return $this->executeCommand('sRemove', func_get_args());
    }

    public function sMove(string $srcKey, string $dstKey, $member): bool
    {
        return $this->executeCommand('sMove', func_get_args());
    }

    public function sIsMember(string $key, $value): bool
    {
        return $this->executeCommand('sIsMember', func_get_args());
    }

    public function sContains(string $key, $value): bool
    {
        return $this->executeCommand('sContains', func_get_args());
    }

    public function sCard(string $key): int
    {
        return $this->executeCommand('sCard', func_get_args());
    }

    public function sPop(string $key)
    {
        return $this->executeCommand('sPop', func_get_args());
    }

    public function sRandMember(string $key)
    {
        return $this->executeCommand('sRandMember', func_get_args());
    }

    public function sInter(string $key1, string $key2, string $keyN = null): array
    {
        return $this->executeCommand('sInter', func_get_args());
    }

    public function sInterStore(string $dstKey, string $key1, string $key2, string $keyN = null)
    {
        return $this->executeCommand('sInterStore', func_get_args());
    }

    public function sUnion(string $key1, string $key2, string $keyN = null): array
    {
        return $this->executeCommand('sUnion', func_get_args());
    }

    public function sUnionStore(string $dstKey, string $key1, string $key2, string $keyN = null)
    {
        return $this->executeCommand('sUnionStore', func_get_args());
    }

    public function sDiff(string $key1, string $key2, string $keyN = null): array
    {
        return $this->executeCommand('sDiff', func_get_args());
    }

    public function sDiffStore(string $dstKey, string $key1, string $key2, string $keyN = null)
    {
        return $this->executeCommand('sDiffStore', func_get_args());
    }

    public function sMembers(string $key): array
    {
        return $this->executeCommand('sMembers', func_get_args());
    }

    public function sGetMembers(string $key): array
    {
        return $this->executeCommand('sGetMembers', func_get_args());
    }

    public function getSet(string $key, $value)
    {
        return $this->executeCommand('getSet', func_get_args(), is_numeric($value));
    }

    public function randomKey(): string
    {
        return $this->executeCommand('randomKey');
    }

    public function select(int $dbindex): bool
    {
        return $this->executeCommand('select', func_get_args());
    }

    public function move(string $key, int $dbindex): bool
    {
        return $this->executeCommand('move', func_get_args());
    }

    public function rename(string $srcKey, string $dstKey): bool
    {
        return $this->executeCommand('rename', func_get_args());
    }

    public function renameKey(string $srcKey, string $dstKey): bool
    {
        return $this->executeCommand('renameKey', func_get_args());
    }

    public function renameNx(string $srcKey, string $dstKey): bool
    {
        return $this->executeCommand('renameNx', func_get_args());
    }

    public function expire(string $key, int $ttl): bool
    {
        return $this->executeCommand('expire', func_get_args());
    }

    public function pExpire(string $key, int $ttl): bool
    {
        return $this->executeCommand('pExpire', func_get_args());
    }

    public function setTimeout(string $key, int $ttl): bool
    {
        return $this->executeCommand('setTimeout', func_get_args());
    }

    public function expireAt(string $key, int $timestamp): bool
    {
        return $this->executeCommand('expireAt', func_get_args());
    }

    public function pExpireAt(string $key, int $timestamp): bool
    {
        return $this->executeCommand('pExpireAt', func_get_args());
    }

    public function keys(string $pattern): array
    {
        return $this->executeCommand('keys', func_get_args());
    }

    public function getKeys(string $pattern): array
    {
        return $this->executeCommand('getKeys', func_get_args());
    }

    public function dbSize(): int
    {
        return $this->executeCommand('dbSize');
    }

    public function auth(string $password): bool
    {
        return $this->executeCommand('auth', func_get_args());
    }

    public function bgrewriteaof(): bool
    {
        return $this->executeCommand('bgrewriteaof');
    }

    public function object(string $string = '', string $key = '')
    {
        return $this->executeCommand('object', func_get_args());
    }

    public function save(): bool
    {
        return $this->executeCommand('save');
    }

    public function bgsave(): bool
    {
        return $this->executeCommand('bgsave');
    }

    public function lastSave(): int
    {
        return $this->executeCommand('lastSave');
    }

    public function type(string $key): int
    {
        return $this->executeCommand('type', func_get_args());
    }

    public function append(string $key, $value): int
    {
        return $this->executeCommand('append', func_get_args());
    }

    public function getRange(string $key, int $start, int $end): string
    {
        return $this->executeCommand('getRange', func_get_args());
    }

    public function substr(string $key, int $start, int $end): string
    {
        return $this->executeCommand('substr', func_get_args());
    }

    public function setRange(string $key, int $offset, $value): int
    {
        return $this->executeCommand('setRange', func_get_args());
    }

    public function strlen(string $key): int
    {
        return $this->executeCommand('strlen', func_get_args());
    }

    public function getBit(string $key, int $offset): int
    {
        return $this->executeCommand('getBit', func_get_args());
    }

    public function setBit(string $key, int $offset, $value): int
    {
        return $this->executeCommand('setBit', func_get_args());
    }

    public function bitCount(string $key): int
    {
        return $this->executeCommand('bitCount', func_get_args());
    }

    public function bitOp(string $operation, string $retKey, string ...$keys): int
    {
        return $this->executeCommand('bitOp', func_get_args());
    }

    public function flushDB(): bool
    {
        return $this->executeCommand('flushDB');
    }

    public function sort(string $key, array $option = null): array
    {
        return $this->executeCommand('sort', func_get_args());
    }

    public function info(string $option = null): string
    {
        return $this->executeCommand('info', func_get_args());
    }

    public function resetStat(): bool
    {
        return $this->executeCommand('resetStat');
    }

    public function ttl(string $key): int
    {
        return $this->executeCommand('ttl', func_get_args());
    }

    public function pttl(string $key): int
    {
        return $this->executeCommand('pttl', func_get_args());
    }

    public function persist(string $key): bool
    {
        return $this->executeCommand('persist', func_get_args());
    }

    public function mset(array $array): bool
    {
        return $this->executeCommand('mset', func_get_args());
    }

    public function mget(array $array): array
    {
        return $this->executeCommand('mget', func_get_args());
    }

    public function msetnx(array $array): bool
    {
        return $this->executeCommand('msetnx', func_get_args());
    }

    public function rpoplpush(string $srcKey, string $dstKey)
    {
        return $this->executeCommand('rpoplpush', func_get_args());
    }

    public function brpoplpush(string $srcKey, string $dstKey, int $timeout)
    {
        return $this->executeCommand('brpoplpush', func_get_args());
    }

    public function zAdd(string $key, array $pairs): int
    {
        $args = [$key];
        foreach ($pairs as $value => $score) {
            $args[] = $score;
            $args[] = $value;
        }
        return $this->executeCommand('zadd', $args);
    }

    public function zRange(string $key, int $start, int $end, bool $withscores = null): array
    {
        return $this->executeCommand('zRange', func_get_args());
    }

    public function zRem(string $key, $member1, $member2 = null, $memberN = null): int
    {
        return $this->executeCommand('zRem', func_get_args());
    }

    public function zRevRange(string $key, int $start, int $end, bool $withscore = null): array
    {
        return $this->executeCommand('zRevRange', func_get_args());
    }

    public function zRangeByScore(string $key, $start, $end, array $options = array()): array
    {
        return $this->executeCommand('zRangeByScore', func_get_args());
    }

    public function zRevRangeByScore(string $key, $start, $end, array $options = array()): array
    {
        return $this->executeCommand('zRevRangeByScore', func_get_args());
    }

    public function zCount(string $key, $start, $end): int
    {
        return $this->executeCommand('zCount', func_get_args());
    }

    public function zRemRangeByScore(string $key, $start, $end): int
    {
        return $this->executeCommand('zRemRangeByScore', func_get_args());
    }

    public function zDeleteRangeByScore(string $key, $start, $end): int
    {
        return $this->executeCommand('zDeleteRangeByScore', func_get_args());
    }

    public function zRemRangeByRank(string $key, int $start, int $end): int
    {
        return $this->executeCommand('zRemRangeByRank', func_get_args());
    }

    public function zDeleteRangeByRank(string $key, int $start, int $end): int
    {
        return $this->executeCommand('zDeleteRangeByRank', func_get_args());
    }

    public function zCard(string $key): int
    {
        return $this->executeCommand('zCard', func_get_args());
    }

    public function zSize(string $key): int
    {
        return $this->executeCommand('zSize', func_get_args());
    }

    public function zScore(string $key, $member)
    {
        return $this->executeCommand('zScore', func_get_args());
    }

    public function zRank(string $key, $member): int
    {
        return $this->executeCommand('zRank', func_get_args());
    }

    public function zRevRank(string $key, $member): int
    {
        return $this->executeCommand('zrevrank', func_get_args());
    }

    public function zIncrBy(string $key, $value, $member)
    {
        return $this->executeCommand('zIncrBy', func_get_args());
    }

    public function zUnion(
        string $Output,
        array $ZSetKeys,
        array $Weights = null,
        string $aggregateFunction = 'SUM'
    ): int {
        
        return $this->executeCommand('zUnion', func_get_args());
    }

    public function zInter(
        string $Output,
        array $ZSetKeys,
        array $Weights = null,
        string $aggregateFunction = 'SUM'
    ): int {
        
        return $this->executeCommand('zInter', func_get_args());
    }

    public function hSet(string $key, string $hashKey, $value)
    {
        return $this->executeCommand('hSet', [$key, $hashKey, $value]);
    }

    public function hSetNx(string $key, string $hashKey, $value): bool
    {
        return $this->executeCommand('hSetNx', func_get_args());
    }

    public function hGet(string $key, string $hashKey)
    {
        return $this->executeCommand('hGet', func_get_args());
    }

    public function hLen(string $key)
    {
        return $this->executeCommand('hLen', func_get_args());
    }

    public function hDel(string $key, string ...$hashKey1): int
    {
        return $this->executeCommand('hDel', func_get_args());
    }

    public function hKeys(string $key): array
    {
        return $this->executeCommand('hKeys', func_get_args());
    }

    public function hVals(string $key): array
    {
        return $this->executeCommand('hVals', func_get_args());
    }

    public function hGetAll(string $key): array
    {
        return $this->executeCommand('hGetAll', func_get_args());
    }

    public function hExists(string $key, string $hashKey): bool
    {
        return $this->executeCommand('hExists', func_get_args());
    }

    public function hIncrBy(string $key, string $hashKey, int $value): int
    {
        return $this->executeCommand('hIncrBy', func_get_args());
    }

    public function hIncrByFloat(string $key, string $field, float $increment): float
    {
        return $this->executeCommand('hIncrByFloat', func_get_args());
    }

    public function hMset(string $key, array $hashKeys): bool
    {
        return $this->executeCommand('hMset', func_get_args());
    }

    public function hMGet(string $key, array $hashKeys): array
    {
        return $this->executeCommand('hMGet', func_get_args());
    }

    public function config(string $operation, string $key, string $value = null)
    {
        return $this->executeCommand('config', func_get_args());
    }

    public function evaluate(string $script, array $args = array(), int $numKeys = 0)
    {
        return $this->executeCommand('evaluate', func_get_args());
    }

    public function evalSha(string $scriptSha, array $args = array(), int $numKeys = 0)
    {
        return $this->executeCommand('evalSha', func_get_args());
    }

    public function evaluateSha(string $scriptSha, array $args = array(), int $numKeys = 0)
    {
        return $this->executeCommand('evaluateSha', func_get_args());
    }

    public function script(string $command, string $script)
    {
        return $this->executeCommand('script', func_get_args());
    }

    public function getLastError(): ?string
    {
        return $this->executeCommand('getLastError');
    }

    public function clearLastError(): bool
    {
        return $this->executeCommand('clearLastError');
    }

    public function dump(string $key)
    {
        return $this->executeCommand('dump', func_get_args());
    }

    public function restore(string $key, int $ttl, $value): bool
    {
        return $this->executeCommand('restore', func_get_args());
    }

    public function migrate(string $host, int $port, string $key, int $db, int $timeout): bool
    {
        return $this->executeCommand('migrate', func_get_args());
    }

    public function time(): array
    {
        return $this->executeCommand('time');
    }

    public function getWithClosure(string $key, int $ttl, \Closure $closure)
    {
        $value = $this->get($key);
        if ($value === false) {
            $value = $closure();
            $this->set($key, $value, $ttl);
        }
        return $value;
    }

    private function executeCommand(string $command, array $args = [], bool $disableSerializer = false)
    {
        if ($disableSerializer) {
            $serializer = $this->getRedis()->getOption(\Redis::OPT_SERIALIZER);
            $this->getRedis()->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE);
            $value = call_user_func_array([$this->getRedis(), $command], $args);
            $this->getRedis()->setOption(\Redis::OPT_SERIALIZER, $serializer);
        } else {
            $value = call_user_func_array([$this->getRedis(), $command], $args);
        }
        return $value;
    }

    private function getRedis(): \Redis
    {
        if (!$this->name) {
            throw new \InvalidArgumentException('Redis name is not specified');
        }
        if ($this->redis === null) {
            $settings = Setting::getConfig();
            $settings = $settings['redis'][$this->name];
            preg_match('/(?P<protocol>\w+):\/\/(?P<host>[0-9a-z._]*):(?P<port>\d+)/', $settings['uri'], $matches);
            $this->redis = new \Redis();
            $this->redis->connect($matches['host'], $matches['port'], 2);
            $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_IGBINARY);
            $prefix = $this->keyNamespace;
            $this->redis->setOption(\Redis::OPT_PREFIX, $prefix);
        }
        return $this->redis;
    }

    public function get(string $key)
    {
        return $this->executeCommand('get', func_get_args());
    }

    public function gets(array $keys): array
    {
        $values = $this->getMultiple($keys);
        foreach ($values as &$value) {
            if ($value === false) {
                $value = null;
            }
        }
        return $values;
    }

    public function increment(string $key, int $by = 1): int
    {
        $args = func_get_args();
        $value = $this->executeCommand('incrBy', $args, true);
        if ($value === false) {
            $this->delete($key);
            $value = $this->executeCommand('incrBy', $args, true);
        }
        return $value;
    }

    public function decrement(string $key, int $value = 1): int
    {
        $args = func_get_args();
        $value = $this->executeCommand('decrBy', $args, true);
        if ($value === false) {
            $this->delete($key);
            $value = $this->executeCommand('decrBy', $args, true);
        }
        return $value;
    }

    public function delete(string $key)
    {
        return $this->executeCommand('del', func_get_args());
    }

    public function deleteMany(array $keys)
    {
        return $this->executeCommand('del', func_get_args());
    }

    public function clear()
    {
        return $this->executeCommand('flushAll');
    }

    public function iterateKeys(string $prefix = null): \Iterator
    {
        return null; //TODO
    }

    public function getFreeMemory(): int
    {
        return 0; //TODO
    }

    public function getAvailableMemory(): int
    {
        return 0; //TODO
    }
}
