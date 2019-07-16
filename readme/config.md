### 环境

目前有四个环境：dev、test、preview、produce

### 配置

- config/config.php 配置入口文件（修改文件后需要 stop & start 服务）

  实际项目请修改 app_name 和 app_flag 项。

  ```php
  <?php
  
  $baseConfig = [
      'app_name' => '用户系统',
      // 应用标识
      'app_flag' => 'YH',
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
  ];
  
  return array_merge(
      $baseConfig,
      ['cron_config' => require_once __DIR__ . '/cron.php'],
      require_once __DIR__ . '/env/' . ENVIRON . '.php'
  );
  
  ```
  
- config/logger.php 日志配置文件（修改改文件后需要 stop & start 服务）

  ```php
  <?php
  
  use WecarSwoole\Util\File;
  
  return [
      'debug' => [
          'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/info.log'),
      ],
      'info' => [
          'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/info.log'),
      ],
      'warning' => [
          'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/warning.log'),
      ],
      'error' => [
          'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/error.log'),
      ],
      'critical' => [
          'mailer' => [
              'driver' => 'default',
              'subject' => '喂车告警',
              'to' => [
                  'songlin.zhang@weicheche.cn' => '张松林'
              ]
          ],
          'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/error.log'),
      ],
      'emergency' => [
          'mailer' => [
              'driver' => 'default',
              'subject' => '喂车告警',
              'to' => [
                  'songlin.zhang@weicheche.cn' => '张松林'
              ]
          ],
          'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/error.log'),
          'sms' => [
              '18588495955' => '张松林'
          ]
      ],
  ];
  ```

- config/cron.php 定时任务配置文件（修改改文件后需要 stop & start 服务）

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

- config/subscriber/subscriber.php 事件订阅配置（修改后 reload 服务即可）

  ```php
  <?php
  
  return [
      \App\Subscribers\UserSubscriber::class
  ];
  ```

- config/api/api.php 外部 api 配置，[详情](./invoke.md)

- config/di/di.php 依赖注入配置（修改后 reload 服务即可）

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

- config/env/$env.php 环境相关配置，如数据库、redis等，目前有四个环境配置：dev.php、test.php、preview.php、produce.php（修改改文件后需要 stop & start 服务）

  ```php
  <?php
  
  use \App\Util\File;
  
  return [
      /**
       * 数据库配置建议以数据库名作为 key
       * 如果没有读写分离，则可不分 read, write，直接在里面写配置信息
       */
      'mysql' => [
          'weicheche' => [
              // 读库使用二维数组配置，以支持多个读库
              'read' => [
                  [
                      'host' => '192.168.85.135',
                      'port' => 3306,
                      'user' => 'root',
                      'password' => 'weicheche',
                      'database' => 'weicheche',
                      'charset' => 'utf8',
                  ]
              ],
              // 仅支持一个写库
              'write' => [
                  'host' => '192.168.85.135',
                  'port' => 3306,
                  'user' => 'root',
                  'password' => 'weicheche',
                  'database' => 'weicheche',
                  'charset' => 'utf8',
              ],
              // 连接池配置
              'pool' => [
                  'size' => 30
              ]
          ],
          // 可以不配置读写分离
          'user_center' => [
              'host' => '192.168.85.135',
              'port' => 3306,
              'user' => 'root',
              'password' => 'weicheche',
              'database' => 'user_center',
              'charset' => 'utf8',
              // 连接池配置
              'pool' => [
                  'size' => 30
              ]
          ]
      ],
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
      ],
      // 缓存配置
      'cache' => [
          'driver' => 'redis', // 可用：redis、file、array、null(一般测试时用来禁用缓存)
          'prefix' => 'usercenter',
          'expire' => 3600, // 缓存默认过期时间，单位秒
          'redis' => 'cache', // 当 driver = redis 时，使用哪个 redis 配置
          'dir' => File::join(EASYSWOOLE_ROOT, 'storage/cache'), // 当 driver = file 时，缓存存放目录
      ],
      // 最低记录级别：debug, info, warning, error, critical, off
      'log_level' => 'debug',
    	'base_url' => 'https://wx.weicheche.cn/v2/refuel',
    	'server' => require_once __DIR__ . '/dev_server.php'
  ];
  ```
  

[返回](../README.md)