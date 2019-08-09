

> 注意：
>
> 1. 由于所有的应用配置都是在 onWorkerStart 中载入的（包括 apollo 的配置），而用户自定义进程启动不会触发该回调函数，因而这些配置无法在自定义进程中使用。
>2. 如果要在自定义进程中使用配置，则需要在自定义进程中单独用 `Config::getInstance()->loadFile` 载入。或者在自定义进程里面执行启动脚本：`\WecarSwoole\Bootstrap::boot()`。
> 3. 另外，reload 指令不会对自定义进程生效，如果改了自定义进程相关代码或配置，必须 stop & start 服务。
>4. 所有环境相关的配置全部放到配置中心，然后通过在 config.php 中引用，程序中不再设置 env/ 配置。



### 配置的修改

- 一些公共的配置（比如 MySQL、Redis 的配置等）以及可能会比较频繁变化的要放到配置中心，其他不怎么变的私有配置可放在配置文件中；
- 放在配置中心的，配置文件中使用 apollo() 助手方法使用；
- 所有配置修改后需要 reload 服务，除了 config/cron.php，此配置修改后需要 stop & start 服务；



### 配置存储方式

Easyswoole 默认采用 swoole table 存储配置信息，其问题是 swoole table 创建时固定了大小（官方设置死了 1024 行，每个 data 字段 1024 字节），如果配置超过限制则存储失败。并且 Easyswoole 内部存储机制是取第一个 “.” 之前的作为 key，这种存法我们很多配置都会超过长度限制。

解决方案有两种：

	1. 不使用 swoole table，而是采用普通数组存储（Easyswoole 的 SplArray，一个 ArrayObject），这种方式没有长度限制，缺点是进程间不共享（Easyswoole 使用 swoole table 的原因即是如此），一个进程修改了配置，只能当钱进程生效；
 	2. 使用动态扩容的 swoole table 方式。具体做法是当发现要存储的字符串超过长度，则创建一个更大容量的新的 swoole table 并将旧数据复制过来。很多语言底层即是采用这种方式实现可变长数组/列表的。

框架采用的是第一种方式。我们假设项目没有动态修改配置的需求，即使有这种需求，也可以通过其他方式实现。



### 配置

- config/config.php 配置入口文件（具体项目可基于 config.example.php 设置自己的配置）

  实际项目请修改 app_name 、app_flag 项。

  其中 apollo() 指使用 apollo 配置中心的配置，具体可点击 apollo() 函数查看详情
  
  ```php
  <?php
  
  use function WecarSwoole\Config\apollo;
  
  $baseConfig = [
      'app_name' => '用户系统',
      // 应用标识
      'app_flag' => 'YH',
      'app_id' => 10017,
      'server' => [
          'modules' => apollo('fw.modules'),
          'app_ids' => apollo('fw.appids'),
      ],
      'logger' => include_once __DIR__ . '/logger.php',
      // 邮件。可以配多个
      'mailer' => [
          'default' => [
              'host' => 'smtp.exmail.qq.com',
              'username' => 'robot@weicheche.cn',
              'password' => 'Chechewei123'
          ]
      ],
      // 并发锁配置
      'concurrent_locker' => [
          'onoff' => 'on',
          'redis' => 'main'
      ],
      // 请求日志配置。默认是关闭的，如果项目需要开启，则自行修改为 on
      'request_log' => [
          'onoff' => 'off',
          // 记录哪些请求类型的日志
          'methods' => ['POST', 'GET', 'PUT', 'DELETE']
      ],
    	    /**
       * 数据库配置建议以数据库名作为 key
       * 如果没有读写分离，则可不分 read, write，直接在里面写配置信息
       */
      'mysql' => [
          'weicheche' => [
              // 读库使用二维数组配置，以支持多个读库
              'read' => [
                  [
                      'host' => apollo('fw.mysql.weicheche.ro', 'host'),
                      'port' => apollo('fw.mysql.weicheche.ro', 'port'),
                      'user' => apollo('fw.mysql.weicheche.ro', 'username'),
                      'password' => apollo('fw.mysql.weicheche.ro', 'password'),
                      'database' => apollo('fw.mysql.weicheche.ro', 'dbname'),
                      'charset' => apollo('fw.mysql.weicheche.ro', 'charset'),
                  ]
              ],
              // 仅支持一个写库
              'write' => [
                  'host' => apollo('fw.mysql.weicheche.rw', 'host'),
                  'port' => apollo('fw.mysql.weicheche.rw', 'port'),
                  'user' => apollo('fw.mysql.weicheche.rw', 'username'),
                  'password' => apollo('fw.mysql.weicheche.rw', 'password'),
                  'database' => apollo('fw.mysql.weicheche.rw', 'dbname'),
                  'charset' => apollo('fw.mysql.weicheche.rw', 'utf8'),
              ],
              // 连接池配置
              'pool' => [
                  'size' => 15
              ]
          ],
          // 可以不配置读写分离
          'user_center' => [
              'host' => apollo('fw.mysql.user_center.rw', 'host'),
              ...
              // 连接池配置
              'pool' => [
                  'size' => 15
              ]
          ]
      ],
      'redis' => [
          'main' => [
              'host' => apollo('fw.redis.01', 'host'),
              'port' => apollo('fw.redis.01', 'port'),
              'auth' => apollo('fw.redis.01', 'auth'),
            	// 连接池配置
              '__pool' => [
                  'max_object_num' => 10,
                  'min_object_num' => 1,
                  'max_idle_time' => 60,
              ]
          ]
      ],
  ];
  
  return array_merge(
      $baseConfig,
      ['logger' => include_once __DIR__ . '/logger.php'],
      ['api' => require_once __DIR__ . '/api/api.php'],
      ['subscriber' => require_once __DIR__ . '/subscriber/subscriber.php'],
      require_once __DIR__ . '/env/' . ENVIRON . '.php'
  );
  
  ```
  
- config/logger.php 日志配置文件

  ```php
  <?php
  
  use WecarSwoole\Util\File;
  
  return [
      'debug' => [
          'file' => File::join(STORAGE_ROOT, 'logs/info.log'),
      ],
      'info' => [
          'file' => File::join(STORAGE_ROOT, 'logs/info.log'),
      ],
      'warning' => [
          'file' => File::join(STORAGE_ROOT, 'logs/warning.log'),
      ],
      'error' => [
          'file' => File::join(STORAGE_ROOT, 'logs/error.log'),
      ],
      'critical' => [
          'mailer' => [
              'driver' => 'default',
              'subject' => '喂车告警',
              'to' => [
                  'songlin.zhang@weicheche.cn' => '张松林'
              ]
          ],
          'file' => File::join(EASYSWOOLE_ROOT, 'logs/error.log'),
      ],
      'emergency' => [
          'mailer' => [
              'driver' => 'default',
              'subject' => '喂车告警',
              'to' => [
                  'songlin.zhang@weicheche.cn' => '张松林'
              ]
          ],
          'file' => File::join(STORAGE_ROOT, 'logs/error.log'),
          'sms' => [
              '18588495955' => '张松林'
          ]
      ],
  ];
  ```

- config/cron.php 定时任务配置文件

  ```php
  <?php
  
  return [
      // 定时任务项目名，同名的多台服务器只会有一台启动定时任务，请务必给不同项目起不同的名字，否则会相互影响
      'name' => 'user-center-platform',
      // 实际项目中 ip 和 redis 配置一个
    	'ip' => ['192.168.0.23'], // 指定 这台服务器执行 crontab，ip 优先于 redis
      'redis' => 'main',
      'tasks' => [
          \App\Cron\Test::class
      ]
  ];
  ```

- config/subscriber/subscriber.php 事件订阅配置

  ```php
  <?php
  
  return [
      \App\Subscribers\UserSubscriber::class
  ];
  ```

- config/api/api.php 外部 api 配置，[详情](./invoke.md)

- config/di/di.php 依赖注入配置

  ```php
  <?php
  
  use App\Foundation\CacheFactory;
  ...
  
  return [
      // 仓储
      'App\Domain\*\I*Repository' => autowire('\App\Foundation\Repository\*\MySQL*Repository'),
      // 缓存
      CacheInterface::class => function () {
          return CacheFactory::build();
      },
      // 日志
      LoggerInterface::class => function () {
          return Logger::getInstance();
      },
      // 事件
      EventDispatcherInterface::class => function () {
          return new EventDispatcher();
      },
      'SymfonyEventDispatcher' =>  get(EventDispatcherInterface::class),
      // DI 容器
      ContainerInterface::class => function () {
          return Di::getInstance()->get('di-container');
      }
  ];
  ```

- config/apollo.php 配置中心 apollo 的配置：

  ```php
  <?php
  /**
   *  Client 配置中心
   */
  return [
      'app_id' => 1298001,
      'server' => [
          'dev' => 'http://192.168.85.203:8080',
          'test' => '192.168.85.201:8080',
          'preview' => '119.23.146.197:8080',
          'produce' => '120.78.9.114:8080',
      ],
      // 需要监听的 namespace
      'namespaces' => [
          'application',
          'fw.appids',
          'fw.modules',
          'fw.mysql.dw.ro',
          'fw.mysql.fw_coupon.ro',
          'fw.mysql.weicheche.rw',
          'fw.redis.01',
          'fw.redis.03'
      ]
  ];
  ```



### apollo 配置中心对接

框架集成了 apollo 监听程序（独立进程运行），当创建新项目时，按照以下步骤操作：

1. 在 apollo 配置中心创建一个新项目（找 fw 人创建）；

2. 登录到 apollo 配置中心管理平台，复制项目的 app_id；

3. 配置 config/apollo.php：配置 app_id 以及需要监听的 namespace（一般需要监听 application、fw.appids、fw.modules 以及相关数据库和 Redis 等公共配置）；

4. 在应用的配置文件中，需要使用 apollo 配置的地方，用 `apollo($namespace, $key = null)` 函数获取即可，如：

   ```php
   'server' => [
   	'modules' => apollo('fw.modules'),
   	'app_ids' => apollo('fw.appids'),
   ],
   ```

5. 启动服务，即可监听，当配置中心相关配置发生改变，服务即拉取并重启；

> **什么样的配置需要放配置中心？**
>
> 公共资源如数据库、消息队列、Redis、Server 列表等需要使用配置中心 fw-framework 项目下的。
>
> 其他可能会比较频繁改变的、涉及敏感信息的。
>
> 基本不会变的配置也可放配置中心，不过建议直接放代码配置文件中更方便些。

> **框架中配置中心同步机制？**
>
> 采用的是 apollo 官方提供的 PHP 程序相同的模式，即采用 60s 长连接轮询 + 拉取的方式。没有采用 Easyswoole 框架自带的扩展，原因是一方面其自带的扩展基于 swoole table，很容易因为字符串长度超过限制而存储失败，另一方面按照 Easyswoole 官方提供的方案，每次启动程序都去配置中心拉取然后设置到内存配置中，那么在拉取到数据之前是无法对外提供服务的。

> **为何没有直接使用现在的同步机制？**
>
> 框架的同步机制和喂车现在的实现方式大致相同。现有的机制是集大成式的，主要针对的是 FPM 模式下 PHP 项目无法很好的实现同步的问题，因而为每台服务器单独部署了 Client 来实现所有 PHP 项目的配置同步。
>
> 这种方式的问题是每新增一个项目（或者迁移项目），需要修改 Client 并在对应服务器部署 Client，管理上比较麻烦。
>
> 在 swoole 模式下，完全可以由每个项目自身维护自身的配置问题，实现项目自治。
>
> 另外框架没有对配置中心的 value 做任何转换，如果配置中心存储的是 json 字符串，则消费程序需自行转换。



[返回](../README.md)