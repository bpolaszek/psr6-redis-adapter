Provides a basic PSR-6 implementation for Redis.

**Warning: this repository is no longer maintained. Read below.** 

The Symfony Core team has developped [a lot of great PSR-6 implementations](https://github.com/symfony/cache), including Redis, that can work with the [Redis extension](https://github.com/phpredis/phpredis) and [Predis](https://github.com/nrk/predis) as well.

Besides, I developped an override of this Symfony's Redis Adapter, which can also retrieve the original expiration time of an already cached item.

If you need this, have a look at [Redis PSR-6 TTLAware Adapter](https://github.com/bpolaszek/redis-psr6-ttl-aware-adapter)
