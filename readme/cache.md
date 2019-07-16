### Cache

使用的 symfony/cache，遵循 PSR-16 规范。

- 配置：

  config/env/$env.php

  ```php
  'cache' => [
      'driver' => 'redis', // 可用：redis、file、array、null(一般测试时用来禁用缓存)
      'prefix' => 'usercenter',
      'expire' => 3600, // 缓存默认过期时间，单位秒
      'redis' => 'cache', // 当 driver = redis 时，使用哪个 redis 配置
      'dir' => File::join(EASYSWOOLE_ROOT, 'storage/cache'), // 当 driver = file 时，缓存存放目录
  ],
  ```

- 使用：

  依赖注入（构造函数或者 di 容器获取）：

  ```php
  use Psr\SimpleCache\CacheInterface;
  ...
  public function __construct(CacheInterface $cache)
  {
    $this->cache = $cache;
    parent::__construct();
  }
  ...
  $this->cache->set('testcache', [343]);
  $this->cache->get('testcache');
  ```

[返回](../README.md)