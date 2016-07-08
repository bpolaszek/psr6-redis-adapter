<?php

namespace BenTools\Cache\Adapter\RedisAdapter;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class RedisCacheItemPool implements CacheItemPoolInterface {

    /**
     * @var \Redis
     */
    protected $redis;

    /**
     * @var array
     */
    protected $deferred = [];

    /**
     * RedisCacheItemPool constructor.
     * @param \Redis $redis
     */
    public function __construct(\Redis $redis) {
        $this->redis = $redis;
    }

    /**
     * @inheritDoc
     */
    public function getItem($key) {
        if ($this->redis->exists($key)) {
            $data = $this->redis->get($key);
            $ttl  = $this->redis->ttl($key);
            $ttl  = $ttl > 0 ? $ttl : 0;
            $item = new RedisCacheItem($key, $data, true, true);
            $item->expiresAfter($ttl);
            return $item;
        }
        else {
            return new RedisCacheItem($key);
        }
    }

    /**
     * @inheritDoc
     */
    public function getItems(array $keys = []) {
        return new \ArrayIterator(array_combine($keys, array_map(function ($key) {
            return $this->getItem($key);
        }, $keys)));
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key) {
        return $this->redis->exists($key);
    }

    /**
     * @inheritDoc
     */
    public function deleteItem($key) {
        $this->redis->delete($key);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function clear() {
        return $this->redis->flushAll();
    }

    /**
     * @inheritDoc
     */
    public function deleteItems(array $keys) {
        call_user_func_array([$this->redis, 'delete'], $keys);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item) {
        if (!$item instanceof RedisCacheItem) {
            throw new \RuntimeException("The CacheItem should be an instance of \\BenTools\\Cache\\Adapter\\RedisAdapter\\RedisCacheItem");
        }
        $timeout = $item->getExpiration()->getTimestamp() - time();
        $this->redis->set($item->getKey(), $item->get(), $timeout);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item) {
        if (!$item instanceof RedisCacheItem) {
            throw new \RuntimeException("The CacheItem should be an instance of \\BenTools\\Cache\\Adapter\\RedisAdapter\\RedisCacheItem");
        }
        $this->deferred[] = $item;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function commit() {
        foreach ($this->deferred AS $key => $item) {
            $this->save($item);
            unset($this->deferred[$key]);
        }
    }

}