<?php namespace Wawa\Memcache;

use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Store;

class MemcacheStore extends TaggableStore implements Store
{

    /**
     * The Memcached instance.
     *
     * @var \Memcache
     */
    protected $memcache;

    /**
     * A string that should be prepended to keys.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Create a new Memcache store.
     *
     * @param  \Memcache $memcache
     * @param  string $prefix
     * @return void
     */
    public function __construct(\Memcache $memcache, $prefix = '')
    {
        $this->memcache = $memcache;
        $this->prefix = strlen($prefix) > 0 ? $prefix . ':' : '';
    }

    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     *
     * @param  array $keys
     * @return array
     */
    public function many(array $keys)
    {
        $return = [];
        foreach ($keys as $key) {
            $return[$key] = $this->get($key);
        }
        return $return;
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string|array $key
     * @return mixed
     */
    public function get($key)
    {
        if (is_array($key)) {
            $key = array_map(function ($key) {
                return $this->prefix . $key;
            }, $key);
        } else {
            $key = $this->prefix . $key;
        }
        if ($value = $this->memcache->get($key)) {
            return $value;
        }
    }

    /**
     * Store multiple items in the cache for a given number of minutes.
     *
     * @param  array $values
     * @param  int $minutes
     * @return void
     */
    public function putMany(array $values, $minutes)
    {
        foreach ($values as $key => $value) {
            $this->put($key, $value, $minutes);
        }
    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string $key
     * @param  mixed $value
     * @param  int $minutes
     * @return void
     */
    public function put($key, $value, $minutes)
    {
        $this->memcache->set($this->prefix . $key, $value, false, $minutes * 60);
    }

    /**
     * Store an item in the cache if the key doesn't exist.
     *
     * @param  string $key
     * @param  mixed $value
     * @param  int $minutes
     * @return bool
     */
    public function add($key, $value, $minutes)
    {
        return $this->memcache->add($this->prefix . $key, $value, false, $minutes * 60);
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string $key
     * @param  mixed $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        return $this->memcache->increment($this->prefix . $key, $value);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string $key
     * @param  mixed $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        return $this->memcache->decrement($this->prefix . $key, $value);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function forever($key, $value)
    {
        return $this->put($key, $value, 0);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string $key
     * @return void
     */
    public function forget($key)
    {
        $this->memcache->delete($this->prefix . $key);
    }

    /**
     * Remove all items from the cache.
     *
     * @return void
     */
    public function flush()
    {
        $this->memcache->flush();
    }

    /**
     * Get the underlying Memcached connection.
     *
     * @return \Memcached
     */
    public function getMemcache()
    {
        return $this->memcache;
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    public function addPrefixForArray(&$key)
    {
        $key = $this->prefix . $key;
    }
}
