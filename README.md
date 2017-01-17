# memcache driver for laravel 5
# install

```shell
composer require "wawa/memcache:2.01"
```
add next classname into config/app.php
```php
Wawa\Memcache\MemcacheServiceProvider::class
```
then

config your cache config

driver => memcache
