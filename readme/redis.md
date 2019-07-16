### Redis

使用 PHP 的 pecl 扩展 phpredis 并使用其自带的连接池。

1. 安装 phpredis 扩展后，配置php.ini：

   extension=redis.so
   redis.pconnect.pooling_enabled=1

2. 配置项目：

   config/env/$env.php

   ```php
   'redis' => [
       'main' => [
           'host' => 'db.redis.wcc.cn',
           'port' => 6379,
           'auth' => 'XEXeh1l6nT3wHL0z'
       ],
       'cache' => [
           'host' => 'db.redis.wcc.cn',
           'port' => 6379,
           'auth' => 'XEXeh1l6nT3wHL0z',
       ],
     	...
   ],
   ```

   其中 main、cache 是自定义的别名，其它地方可以引用。

3. 使用：

   ```php
   use WecarSwoole\RedisFactory;
   ...
   $redis = RedisFactory::build('main');
   $redis->set('testredis', 'abcdef');
   $result = $redis->get('testredis');
   ```

> 注意：由于 Redis 使用的是 PHPredis 扩展自带的连接池技术，应用层不需要再实现连接池，每次使用直接调用 RedisFactory 创建即可。

[返回](../README.md)