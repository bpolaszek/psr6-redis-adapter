<?php

namespace BenTools\Cache\Adapter\RedisAdapter;

use Psr\Cache\CacheItemInterface;

class RedisCacheItem implements CacheItemInterface {
    
    /**
     * @var
     */
    private $key;

    /**
     * @var null
     */
    private $value;

    /**
     * @var bool
     */
    private $exists = false;

    /**
     * @var bool
     */
    private $isHit = false;

    /**
     * @var \DateTimeInterface|null
     */
    private $expiresAt;

    /**
     * RedisCacheItem constructor.
     * @param      $key
     * @param null $value
     * @param bool $exists
     * @param bool $isHit
     * @param      $expiresAt
     */
    public function __construct($key, $value = null, $exists = false, $isHit = false, $expiresAt = null) {
        $this->key       = $key;
        $this->value     = $value;
        $this->exists    = $exists;
        $this->isHit     = $isHit;
        $this->expiresAt($expiresAt);
    }

    /**
     * @inheritDoc
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function get() {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function set($value) {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function isHit() {
        return $this->isHit;
    }

    /**
     * @inheritDoc
     */
    public function exists() {
        return $this->exists;
    }

    /**
     * @inheritDoc
     */
    public function expiresAt($expiration) {

        switch ($expiration) {

            case null:
                $this->expiresAt = null;
                break;

            case $expiration instanceof \DateTimeInterface:
                $this->expiresAt = $expiration;
                break;

            case is_integer($expiration):
                $this->expiresAt = \DateTime::createFromFormat('U', $expiration);
                break;

            default:
                throw new InvalidArgumentException("Invalid expiration");
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAfter($time) {

        switch ($time) {

            case null:
                $this->expiresAt = null;
                break;

            case $time instanceof \DateInterval:
                $this->expiresAt = (new \DateTime())->add($time);
                break;

            case is_integer($time):
                $this->expiresAt = new \DateTime(sprintf('+%d seconds', $time));
                break;

            default:
                throw new InvalidArgumentException("Invalid time");
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExpiration() {
        return $this->expiresAt ? $this->expiresAt : new \DateTime();
    }
}