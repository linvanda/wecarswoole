### Redis

使用 PHP 的 pecl 扩展 phpredis（worker/task 进程中已经对其做了协程化，自定义进程如果需要使用，需要自己做协程化（调用 swoole 相关函数））。

1. 安装 phpredis 扩展后，配置php.ini：

   extension=redis.so
   
2. 配置项目：

   config/config.php

   ```php
   'redis' => [
       'main' => [
           'host' => apollo('fw.redis.01', 'redis.host'),
           'port' => apollo('fw.redis.01', 'redis.port'),
           'auth' => apollo('fw.redis.01', 'redis.auth'),
           // 连接池配置
           '__pool' => [
             'max_object_num' => 10,
             'min_object_num' => 1,
             'max_idle_time' => 60,
           ]
       ],
     	...
   ],
   ```

   其中 main 是自定义的别名，其它地方可以引用。

3. 使用：

   ```php
   use WecarSwoole\RedisFactory;
   ...
   $redis = RedisFactory::build('main');
   $redis->set('testredis', 'abcdef');
   $result = $redis->get('testredis');
   ```

### 实现

由于很多外部扩展（如一些 Cache 类库）依赖于 Redis 扩展，而在 Swoole 常驻进程中，这些单例对象（如 Cache 实例）会一直持有同一个Redis 连接实例，在长时间不连接的情况下 Redis 服务器会断开连接，从而造成后续操作失败。

因而框架创建了 RedisProxy 代替了原生的 \Redis，该扩展拦截 \Redis 的所有操作，在操作前先从连接池获取连接对象，如果连接对象已经和服务器断开连接，则重连，然后执行操作，最后归还连接。

外界无需关注实现细节，用 RedisFactory::build() 获取 Redis 实例当作 \Redis 使用即可。

[返回](../README.md)