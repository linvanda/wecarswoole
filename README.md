WecarSwoole
----
### 简介

WecarSwoole 是基于 EasySwoole 开发的适用于喂车业务系统的 Web 开发框架。

[EasySwoole 使用说明](http://www.easyswoole.com)



### 环境要求

- PHP >= 7.2

- Swoole >= 4.3.0

- phpredis 扩展

  > php.ini 配置文件需加入 `redis.pconnect.pooling_enabled=1` 开启 phpredis 连接池



### 创建新项目

1. 创建项目目录 myproject;

2. cd myproject 并创建 `composer.json`，加入以下代码：

   ```json
   {
       "name": "wechar/wecarswoole_proj",
       "description": "your project name",
       "type": "project",
       "require": {
           "framework/wecarswoole": "dev-master"
       },
     	"autoload": {
           "psr-4": {
               "App\\": "app/",
               "Test\\": "tests/"
           }
       },
       "repositories": {
           "0": {
               "type": "vcs",
               "url": "https://gitlab4.weicheche.cn/framework/wecarswoole.git"
           },
           "1": {
               "type": "vcs",
               "url": "https://gitlab4.weicheche.cn/dev/locker.git"
           },
           "2": {
               "type": "vcs",
               "url": "https://gitlab4.weicheche.cn/dev/mysql.git"
           },
           "3": {
               "type": "vcs",
               "url": "https://gitlab4.weicheche.cn/framework/wecar_easyswoole.git"
           },
           "4": {
               "type": "vcs",
               "url": "https://gitlab4.weicheche.cn/framework/easyswoole_http.git"
           },
           "packagist": {
               "type": "composer",
               "url": "https://packagist.laravel-china.org"
           }
       }
   }
   ```

3. 执行 `composer install`

4. 执行 `php vendor/bin/wecarswoole install` 安装 WecarSwoole 框架

5. 修改配置文件

6. 启动：`php easyswoole start d --env=dev` (—env : dev、test、preview、produce，d 表示后台运行)

7. 停止：`php easyswoole stop`

8. 其他指令参见 easyswoole 官网

**注意**

> 1. 由于我们目前没有私有 composer 仓库，故上面的配置文件采用 vcs 仓储模式加载组件，包括以后开发的新组建也要将 gitlab 地址加入到这里面（必须加入到项目的 composer.json 中，加入到下级组件的 composer.json 是无效的）；
>
> 2. 当搭建了私有 composer 仓库后，可以删掉这些 `vcs`  配置，只需将 `packagist` 项改成我们自己的私有仓库地址即可；
> 4. 当执行 composer 命令出错时（如 install、update 等），请在后面加 -vvv 查看详细信息（如 composer install -vvv）；
> 5. 项目不要提交 vendor 目录到 git 中；
> 6. 关于国内镜像： https://packagist.phpcomposer.com 没人维护了，现在用了 https://packagist.laravel-china.org，虽然 Laravel China 声称会长期维护，不过不可保证，可考虑搭建内部 composer 库；



### 在现有项目上开发

- 根据前面步骤创建项目并提交后，其他人 clone 下来执行 `composer install` 即可。
- 生产环境部署：部署平台（如 walle）需要增加指令：`composer install`，该指令会根据 composer.lock 文件信息安装指定版本的库。
- **不要在生产环境执行 `composer update`！**
- **不要每个开发人员随便在本地执行 `composer update`！**
- 一句话：**谨慎执行 `composer update`**，因为 composer update 指令会根据 composer.json 中的版本配置信息获取符合版本约束的最新代码并更新 composer.lock 文件，如果每个开发人员都去执行 composer update，那么 composer.lock 文件会频繁变动，造成不稳定，可能会出现莫名其妙的问题。



### 给项目引入新的包

1. 团队中某个成员在项目根目录下执行：`composer require vendor/package_name`，如 `composer require monolog/monolog ` ；
2. 提交到 gitlab；
3. 其他人 `git pull --rebase` 并执行 `composer install` 安装新的包；
4. 开发完成，发布；



### 更新包文件

1. 团队中某个成员在项目根目录下执行 `composer update vendor/package_name`，如 `composer update framework/wecarswoole`；
2. 提交到 gitlab；
3. 其他人 `git pull --rebase` 并执行 `composer install` 安装新的包；
4. 开发完成，发布；

> 注意：不要执行 `composer update` 一次更新所有包，要更新哪个就更新哪个。



### 语义化版本控制

使用 composer 做依赖管理时（包括我们自己开发 composer 包），需要遵循语义化版本控制：

版本格式：**主版本号.次版本号.修订号**，版本号递增规则如下：

1. **主版本号**：当你做了**不兼容**的 API 修改；
2. **次版本号**：当你做了**向下兼容的功能性新增**；
3. **修订号**：当你做了**向下兼容的问题修正**；

更多信息请参见 [语义化版本控制](https://semver.org/lang/zh-CN/)

> 即是说，我们的包向外发布之后，不能随便修改其内容，一旦修改，就需要同时增加新的版本号（打 tag），版本号命名需遵循以上约束。



以上几点是 composer 的常见使用方式，大家记住最重要的一点：**谨慎执行任何导致 composer.lock 文件发生变化的操作指令（如update，require 等）**，因为一旦 composer.lock 发生变化并发布生产，生产环境将应用这些变化。



### 系统设计要点

- 可扩展性
- 容易和第三方系统对接
- 可测试
- 遵循 [PSR 规范](https://www.php-fig.org)
- 组合优于继承：
  - 类继承层次不要过深，一般不要超过3层。
  - 不要在基类写太多功能，基类功能越多越笨重不灵活。
  - 优先使用多个类组合完成功能，而不是全塞到基类里面实现。



### 目录结构

- project_root
  - app
    - Cron
    - Domain
      - Events
    - Exceptions
    - Foundation
      - Repository
      - Util
    - Http
      - Controllers
        - V1
          - $modules
      - Middlewares
      - Routes
    - Process
    - Subscribers
    - Tasks
  - config
    - api
    - di
    - env
    - subscriber
  - storage
    - app
    - cache
    - di
    - logs
    - temp
  - vendor
  - tests

#### 说明

app/ : 项目代码目录

app/Cron/ : 定时任务

app/Domain/ : 业务（领域）逻辑，核心目录

app/Domain/Events/ : 领域事件

app/Exceptions/ : 异常类定义

app/Foundation/ : 基础设施（如仓储实现类等）

app/Foundation/Repository/: 仓储实现

app/Foundation/Util/: 工具

app/Http/ : http 路由、控制器，对外暴露 http api

app/Http/Controllers/: http 控制器

app/Http/Controllers/V1/: 版本

app/Http/Controllers/V1/$modules/: 模块划分（模块名具体而定）

app/Http/Middlewares/: 中间件（如路由中间件）

app/Http/Routes/: 路由定义

app/Process/ : 自定义进程

app/Subscribers/ : 事件订阅者

app/Tasks/ : 异步任务

config/ : 配置文件

config/api/ : 外部 api 定义

config/di/ : 依赖注入配置

config/env/ : 环境相关配置（如数据库、redis 等）

config/subscriber/ : 订阅者配置

storage/ : 存储相关（日志、缓存、临时文件等）

storage/app/ : 应用程序存储（如第三方证书等）

storage/cache/ : 文件缓存

storage/di/ : di 缓存

storage/logs/ : 文件日志

storage/temp/ : 临时文件

vendor/ : 第三方库

tests/ : 单元测试

dev.php : 开发环境（包括开发、测试、预发布）swoole_server 配置

produce.php : 生产环境 swoole_server 配置

easyswoole : 服务启动/停止/重启脚本

EasySwooleEvent.php : 全局事件



> 注：以上目录划分确定了基本的开发规范，但实际开发过程中并不限制一定只能划分以上目录，各项目可以在此基础上根据实际需要开设额外的目录。



### 设计思想

- 借鉴于**领域驱动设计(DDD)**思想。有别于传统 MVC 分层设计，在 DDD 中，系统划分为四个层次：

  - **表示层**。展示UI/数据、接收用户的输入，直接和用户打交道（用户可能是人也可能是其他系统）。如前端交互。
  - **应用层**。从用户的维度定义系统需要完成的**任务**。应用层只定义任务，不负责具体实现。如这里的 Controller、Cron（实际上它们也承担了部分表示层职责）、Task、Subscriber等。
  - **领域层**。业务逻辑的具体实现。应用层调用领域层实现具体的任务。这里的 Domain目录下的代码。
  - **基础设施层**。提供诸如 DB、Cache、SESSION、Email、Log 等业务无关的基础支持。

  [领域驱动设计](https://www.jdon.com/ddd.html)

  [领域驱动设计分层模型](https://www.jianshu.com/p/c405aa19a049)

![分层](./readme/layer.jpg)



### 框架中的分层说明

- **表示层 + 应用层**：框架并没有对表示层和应用层做严格的划分，后面提到**应用层**也是指部分表示层+应用层。严格来说，表示层诸如 web（h5、json等）、web socket等客户端需要的数据格式以及提供的输入由表示层作转换处理，然后交由应用层，且多个表示层可以共用同一个应用层。我们的框架中路由+Controller 完成表示层+应用层的工作（不光如此，其他类型的 handle 如事件订阅者也兼顾表示层和应用层的工作）。框架处于复杂性考虑没有引入应用服务的概念，不过熟悉 DDD 的话根据实际需要可以自行引入。

  框架中的应用层：

  - Cron/：定时任务；
  - Http/: http api (路由、控制器)；
  - Subscribers/: 事件订阅者；
  - Tasks/: 异步任务；

  应用层应当尽可能简单，不能写业务逻辑（业务逻辑要写在 Domain 中），主要是用来定义用例维度的**任务**（如用户注册）。

- **领域层**。放在 Domain/ 目录中。这里放具体的业务逻辑代码，属于系统核心。Domain/ 底下可根据实际需要自由创建目录，自由组织代码。不过根据 DDD 通行做法，会分成以下几大概念：

  （以下仅作为概念阐述，不了解没关系）

  - Service（服务)。全称是**领域服务**(相对于应用服务)。Service 是用来组织其他实体类或其他 Service 实现业务逻辑的。外界（如 Controller）一般调用 Service 完成任务。Service 应当保持简单（即自己不实现业务细节，而是通过调用、组织其他类来实现功能），而且是**无状态的**（即 Service 不能在属性中保存业务状态信息）。

    另一个常见的 Service 是外部接口调用，如调用外部的积分系统，此时一般我们会创建一个单独的 Service 封装接口调用。

  - Entity（实体)。Entity 对应业务中的"那一个"东西，一般在数据库有对应一条记录。Entity 有唯一标识。

  - Value Object(值对象)。和 Entity 不同，Value Object 不区分"那一个"，Entity 通过标识辨识，而 Value Object 通过属性辨识。

  - Aggregation（聚合）。一个或多个 Entity 集聚成一个 Aggregation。外界跟 Aggregation 打交道，而不是直接跟每个 Entity 打交道。聚合有聚合根（Aggregation Root），它是一个 Entity。很多时候，一个 Entity 就是一个 Aggregation。

  - Domain Event（领域事件）。在领域对象中触发的事件。一般我们采用事件来接耦非主流业务，保持主流程的清晰简洁。

  - Repository（仓储）。实现 Entity 的存取。仓储是领域模型和数据存储（基础设施）之间的桥梁，它知晓领域类的细节以及数据存储的细节。一般地，在 Domain/ 中定义 Repository 接口，而在基础设施中定义实现，然后通过依赖注入来使用。仓储也应该是**无状态**的。

  - **领域层**简化版：

    不熟悉 DDD 甚至是面向对象设计的话，上面的概念会难以理解，实际操作中可以作如下简化：

    - Service（服务）。同上。服务主要起协调、组合的作用，其本身不应提供具体的业务实现；
    - Entity（实体）。我们将上面的 Entity、Value Object、Aggregation 不做区分统一看作 实体。每个实体类都不大，负责的功能比较单一，多个实体组合/聚合完成一项完整的功能。总之，你可以把这里的实体看作类似之前的 Logic，不过是进行了职责划分的多个类的有机组合；
    - Domain Event（领域事件）。相当于钩子，采用的是观察者模式，实现复杂业务解耦；
    - Repository（仓储）。同上。

  - 注意：

    - 领域层的代码应当是可测试的（单元测试）；
    - 领域层对其他层的依赖应当通过依赖注入实现，而不能在领域层直接 new 其他层的对象；
    - 领域层和其他层通信一般是基于接口的（面向接口编程）；
    - **禁止在领域层直接使用 Session、Request、Response、Cookie、Header、Container、DI、Config 等全局变量和框架相关的东西**，保证业务逻辑代码是框架无关的而且是可测试的；

- **基础设施层**。提供诸如 DB、Cache、SESSION 等业务无关的基础支持。



### 调用关系图解

![调用关系](./readme/invoke.jpg)



##### 说明

- 应用层的控制器/处理器调用领域层的 Service 处理任务；
- 应用层的控制器/处理器调用仓储 Repository 直接查询数据（针对那种不需要业务逻辑处理的数据展示，我们可以在控制器中直接调用仓储，返回需要的 DTO 对象，此乃**用例查询优化**）；
- Service 可以调用另一个 Service；
- Service 可以调用实体 Entity 来实现功能；
- Service 可以调用仓储获得 Entity；
- Entity 可以调用其它 Entity；
- Entity 可以调用 Service；
- Entity 可以发布事件供外围程序处理；



### 使用详解

----

#### 环境

目前有四个环境：dev、test、preview、produce



#### 配置

- config/config.php 配置入口文件

  实际项目请修改 app_name 和 app_flag 项。

  ```php
  <?php
  
  use \WecarSwoole\Util\File;
  
  $baseConfig = [
    	// 具体应用请修改
    	'app_name' => '应用名称',
      'app_flag' => 'SY', // 应用标识
      // 日志配置，可配置：file（后面对应目录），mailer（后面对应邮件配置）
      'logger' => [
          'debug' => [
              'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/debug_info.log'),
          ],
          'info' => [
              'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/debug_info.log'),
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
                  'subject' => '喂车邮件告警',
                  'to' => [
                  ]
              ],
              'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/error.log'),
          ]
      ],
      // 邮件。可以配多个
      'mailer' => [
          'default' => [
              'host' => 'smtp.exmail.qq.com',
              'username' => 'robot@weicheche.cn',
              'password' => 'Chechewei123'
          ]
      ],
  ];
  
  return array_merge(
      $baseConfig,
      ['cron_config' => require_once __DIR__ . '/cron.php'],
      ['api_config' => require_once __DIR__ . '/api/api.php'],
      ['subscriber' => require_once __DIR__ . '/subscriber/subscriber.php'],
      require_once __DIR__ . '/env/' . ENVIRON . '.php'
  );
  ```

- config/cron.php 定时任务配置文件

  ```php
  <?php
  
  return [
      // 定时任务项目名，同名的多台服务器只会有一台启动定时任务，请务必给不同项目起不同的名字，否则会相互影响
      'name' => 'user-center-platform',
      // crontab 需要 redis
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
      \App\Subscribers\User::class,
  ];
  ```

- config/api/api.php 外部 api 配置，详情见后文说明

- config/di/di.php 依赖注入配置

  ```php
  <?php
  
  use App\Foundation\CacheFactory;
  use Psr\SimpleCache\CacheInterface;
  use Psr\Log\LoggerInterface;
  use Psr\EventDispatcher\EventDispatcherInterface;
  
  return [
      /**
       * 仓储依赖注入配置
       * 默认取 Foundation\Repository 下同模块的 MySQL*Repository
       * 如果有自定义的，要放到默认配置的前面，否则不会生效
       */
      'App\Domain\*\I*Repository' => \DI\create('\App\Foundation\Repository\*\MySQL*Repository'),
      // 缓存
      CacheInterface::class => \DI\factory([CacheFactory::class, 'build']),
      // 日志
      LoggerInterface::class => \DI\create(\WecarSwoole\Logger::class),
      // 事件
      EventDispatcherInterface::class => \DI\create(\Symfony\Component\EventDispatcher\EventDispatcher::class),
      'SymfonyEventDispatcher' => \DI\get(EventDispatcherInterface::class),
  ];
  ```

- config/env/$env.php 环境相关配置，如数据库、redis等，目前有四个环境配置：dev.php、test.php、preview.php、produce.php

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
  ];
  ```



#### Http 路由

- 系统对外暴露的所有接口都要进行显式的路由定义；

- 定义文件：app/Http/Routes/ 中定义，如 User.php 定义用户相关路由；

- 基类：`\WecarSwoole\Http\Route`：

  ```php
  <?php
  
  namespace WecarSwoole\Http;
  
  /**
   * 路由基类
   * 中间件的注册方式：
   *  1. 类全局注册：在子类的$middleware数组中配置中间件类名，则此类中定义的所有路由共用该中间件
   *  2. 路由注册：在设置路由时于参数中指定中间件类名，则仅用于该路由
   * 中间件执行顺序取决于注册顺序，类全局的先于特定路由的
   */
  abstract class Route
  {
      use MiddlewareHelper;
  
      protected $routeCollector;
  
      public function __construct(RouteCollector $collector)
      {
          $this->routeCollector = $collector;
      }
  
      public function get(string $routePattern, string $handler, array $middleware = [])
      {
          $this->addRoute(['GET'], $routePattern, $handler, $middleware);
      }
  
      public function post(string $routePattern, string $handler, array $middleware = [])
      {
          $this->addRoute(['POST'], $routePattern, $handler, $middleware);
      }
  
      public function put(string $routePattern, string $handler, array $middleware = [])
      {
          $this->addRoute(['PUT'], $routePattern, $handler, $middleware);
      }
  
      public function delete(string $routePattern, string $handler, array $middleware = [])
      {
          $this->addRoute(['DELETE'], $routePattern, $handler, $middleware);
      }
  
      public function addRoute(array $methods, string  $routePattern, string $handler, array $middleware = [])
      {
          ...
      }
  
      /**
       * 子类在此处添加路由
       * @return mixed
       */
      abstract function map();
  }
  ```

  MiddlewareHelper 提供了以下方法用于添加中间件：

  ```php
  <?php
  
  namespace WecarSwoole\Middleware;
  
  /**
   * 中间件操作助手
   * Trait MiddlewareHelper
   * @package WecarSwoole\Middleware
   */
  trait MiddlewareHelper
  {
      private $middleware = [];
      private $middlewareObjects = [];
  
      /**
       * 设置中间件列表，该方法会重置之前设置过的值
       * @param array $middlewareNameList
       */
      public function setMiddleware(array $middlewareNameList)
      {
          $this->middleware = $middlewareNameList;
      }
  
      /**
       * 返回中间件类名数组
       * @return array
       */
      public function getMiddleware()
      {
          return $this->middleware;
      }
  
      /**
       * 追加中间件
       * @param string|array $middlewareName
       */
      public function appendMiddleware($middlewareName)
      {
          if (is_string($middlewareName)) {
              $this->middleware[] = $middlewareName;
          } else {
              $this->middleware = array_merge($this->middleware, $middlewareName);
          }
      }
  
      /**
       * 删除中间件
       * @param string $middlewareName
       */
      public function removeMiddleware(string $middlewareName)
      {
          $index = array_search($middlewareName, $this->middleware);
          if ($index !== false) {
              unset($this->middleware[$index]);
          }
      }
     
      ...
  }
  ```

- 路由类需继承 `WecarSwoole\Http\Route` 抽象类并实现 map() 方法定义具体路由，使用 get、post、put、delete 定义 RESTful API 接口；

  例：

  ```php
  namespace App\Http\Routes;
  
  use App\Foundation\Http\Route;
  
  class Users extends Route
  {
      public function map()
      {
          // 添加用户
          $this->post('/v1/users', '/V1/Users/add');
          // 用户-商户关系绑定
          $this->post('/v1/merchants/{merchant}/users/{uid}', '/V1/MerchantUsers/bind');
          // 修改用户信息
          $this->put('/v1/users/{uid}', '/V1/Users/edit');
          // 查询用户信息
          $this->get('/v1/users/{uid}', '/V1/Users/info');
          // 查询商户-用户列表
          $this->get('/v1/merchants/{merchant}/users', '/V1/MerchantUsers/getUsers');
          // 合并用户
          $this->post('/v1/users/merge', '/V1/Merge/mergeUsers');
          $this->delete('/v1/users/{uid}', 'V1/Users/delete');
      }
  }
  ```

- 框架提供了一个 `\WecarSwoole\Http\ApiRoute`基类，继承该类的路由都需走 api 鉴权（我们目前的鉴权方式）。

##### 路由定义

使用 [fast-route](https://github.com/nikic/FastRoute) 规则。

```
/users
/users/{id:\d+}			-- 正则匹配：数字
/articles/{id:\d+}[/{title}]	-- 可选参数：title
```

##### 路由中间件

可以添加中间件进行路由信息拦截，如用来做鉴权（api鉴权、登录验证等）。如果中间件抛出异常，则终止请求执行，返回错误给用户。

- 在 app/Http/Middlewares/ 中创建中间件类，需实现 `\WecarSwoole\Middleware\IRouteMiddleware` 接口（并实现其 `handle(Request $request)` 方法）;
- 在路由类的构造函数中调用 `$this->setMiddleware(array $middlewareNameList)` 给路由添加中间件，参数为中间件类名。该做法会让该路由类以及继承该路由类的路由全部应用该中间件；
- 还可以针对单独的路由添加中间件：在调用 get、post、put、delete 方法设置路由时第三个参数可以传入中间件列表，格式同上；

实践：设置两个路由指向同一个控制器，这两个路由一个暴露给公司内部，一个暴露给外部第三方，两者使用不同的鉴权机制，而实现的功能相同（因而使用同一个控制器）。可以创建两个路由父类，两者使用不同的鉴权中间件，一个对内，一个对外，所有内部 api 都继承对内的那个父类，对外 api 则继承另一个。

##### RESTful API

建议使用 RESTful 风格 api 定义。关于 RESTful 请参见 [Restful API 最佳实践](http://www.ruanyifeng.com/blog/2018/10/restful-api-best-practices.html)



#### 控制器

严格来说叫 Http 控制器。目录：`app/Http/Controllers/$version`。

控制器属于**处理器**的一种，属于应用层程序，因而控制器中不能写业务逻辑，通过调用 Domain 层实现业务处理。

- 所有的控制器需继承 `WecarSwoole\Http\Controller`；
- 控制器中除了对外暴露的接口，不要写 public 方法；
- **控制器中禁止写 private 属性，必须为 protected 的**。因为框架使用了对象池技术，每次请求结束后的清理程序无法清理 private 属性，从而 priate 属性值会保留到后面的请求，从而造成污染；
- 禁止在基类控制器对外暴露 api。基类控制器要保持尽可能简单；
- 禁止在控制器中使用静态属性（静态属性不会在每次请求后重置，会造成数据混乱）；
- 构造器中一定要在最后（而不是前面）再调用 parent::__construct()，否则后续请求无法访问这里面设置的属性；
- 建议使用依赖注入从控制器的构造函数注入 Service、Repository 等。
- 注意：通过依赖注入注入的依赖仅仅会创建一次，由于使用了对象池技术，后续会复用这些对象。因而，**依赖注入并赋值给控制器属性的对象必须是无状态的（如仓储、服务等）**，否则会造成混乱。
- 控制器是有版本控制的，但 Domain 没有，Domain 一般需要保持业务一致性。
- 目前的基类控制器提供的便捷方法：
  - `$this->params($key = null)`：获取输入参数，不分请求方式（POST、GET 等）；
  - `$this->return($data = [], int $status = 200, string $msg = '')`：返回 json 数据；

对于命令类操作（需要修改数据的，涉及到业务逻辑的），一般是在控制器中注入并使用 Domain Service，对于查询类操作（仅获取数据用于展示，不涉及到多少业务逻辑处理的），一般可以在控制器中注入并使用 Repository，Repository 返回 DTO 对象，控制器中将 DTO 对象转成数组并格式化成 json 返回。

> 注：不建议在控制器中进行鉴权（如 api 鉴权、登录验证等），因为这样的话控制器就只能局限于当前鉴权上下文使用（如只能在用户登录状态下使用）。建议将鉴权操作前置到路由层（通过路有中间件实现，这点同 Laravel），路由层如果鉴权通过后，将必要信息追加到请求参数中传递给控制器。



#### 领域

##### 领域事件

可以在领域对象（领域服务、实体）中发布领域事件，实现观察者模式解耦非核心业务逻辑。

- 定义事件类：在 app/Domain/Events/ 中定义，需继承 `Symfony\Contracts\EventDispatcher\Event`，如：

  ```php
  namespace App\Domain\Events;
  
  use App\Domain\User\User;
  use Symfony\Contracts\EventDispatcher\Event;
  
  class UserAddedEvent extends Event
  {
      private $user;
  
      public function __construct(User $user)
      {
          $this->user = $user;
      }
  
      public function getUser(): User
      {
          return $this->user;
      }
  }
  ```

- 发布事件：

  ```php
  use Psr\EventDispatcher\EventDispatcherInterface;
  ...
  public function __construct(EventDispatcherInterface $eventDispatcher)
  {
    $this->eventDispatcher = $eventDispatcher;
    parent::__construct();
  }
  ...
  $this->eventDispatcher->dispatch(new YourEvent(...$params));
  ```

- 订阅事件：

  - 定义：在 app/Subscribers/ 目录中定义，需实现 `Symfony\Component\EventDispatcher\EventSubscriberInterface` 接口并实现 `getSubscribedEvents()` 方法，如：

    ```php
    <?php
    
    namespace App\Subscribers;
    
    use App\Domain\Events\UserAddedEvent;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    
    /**
     * 用户事件订阅者
     * Class User
     * @package App\Subscribers
     */
    class User implements EventSubscriberInterface
    {
        /**
         * Returns an array of event names this subscriber wants to listen to.
         * For instance:
         *  * ['eventName' => 'methodName']
         *  * ['eventName' => ['methodName', $priority]]
         *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
         *
         * @return array The event names to listen to
         */
        public static function getSubscribedEvents()
        {
            return [
                UserAddedEvent::class => [
                    ['initLevel'],
                    ['initCard']
                ],
            ];
        }
    
        public function initLevel(UserAddedEvent $event)
        {
            echo "初始化用户等级。用户:" . $event->getUser()->getId() ."\n";
        }
    
        public function initCard(UserAddedEvent $event)
        {
            echo "初始化用户储值卡。用户:" . $event->getUser()->getId() ."\n";
        }
    }
    ```

注意：订阅者和控制器一样，属于**处理程序**，里面不应该写业务逻辑（业务逻辑还是要调 Domain/下面的类）。

##### 仓储

仓储是领域对象（实体）和存储设施（如 MySQL 数据库）之间的桥梁，它知道两方面内容：领域对象属性细节和存储细节。

行业实践上，分成仓储接口和仓储实现，在 Domain/ 中定义仓储接口（如 `IUserRepository`），在 Foundation/Repository/ 中定义具体实现（如 `MySQLUserRepository`）。Domain/ 中只依赖于接口，不依赖实现，这样好处是后面可以随意更改实现（如换成 MongoDB）。

框架默认使用的是 MySQL 实现，在 `config/di.php` 中定义：   `'App\Domain\*\I*Repository' => \DI\create('\App\Foundation\Repository\*\MySQL*Repository')`，这里要求接口所在的目录结构和Foundation/Repository/ 目录结构一致，且命名需符合规范（将 I 替换成 MySQL，其它不变）。如果需要更改实现，需在此处配置（注意放到这条之前，否则不会用到。具体参见 [PHP-DI](http://php-di.org)）。

**MySQL 版仓储不允许跨库。**

- 仓储接口定义：一般直接放在 app/Domain/$module/ 下面（对于复杂的模块也可以定义专门子目录）：

  ```php
  interface IUserRepository
  {
      /**
       * 添加用户
       * @param User $user
       * @return int|bool 成功返回 uid，失败返回 false
       */
      public function add(User $user);
  
      /**
       * 根据 uid 获取用户
       * @param int $uid
       * @return User
       */
      public function getById(int $uid): ?User;
  }
  ```

- 仓储实现：一般放在 app/Foundation/Repository/$module/ 下面（对应上面的目录结构）：

  ```php
  class MySQLUserRepository extends MySQLRepository implements IUserRepository
  {
      /**
       * 添加用户
       * @param User $user
       * @return int|bool 成功返回 uid，失败返回 false
       */
      public function add(User $user)
      {
          $this->query->insert('users')->values([
              [
                  'name' => $user->name,
                  'phone' => $user->phone,
                  'nickname' => $user->nickname,
              ]
          ])->execute();
  
          return $this->query->lastInsertId();
      }
  
      /**
       * 根据 uid 获取用户
       * @param int $uid
       * @return User
       * @throws \App\Exceptions\PropertyNotFoundException
       * @throws \App\Exceptions\InvalidOperationException
       */
      public function getById(int $uid): ?User
      {
          $userInfo = $this->query->select('*')->from('users')->where(['uid' => $uid])->one();
  
          if ($userInfo) {
              $user = new User($userInfo['phone'], $userInfo['name'], $userInfo['nickname']);
              $user->setId($userInfo['uid']);
              return $user;
          }
  
          return null;
      }
  }
  ```

- 仓储方法入参可接收类型：Entity 类型、基本数据类型；
- 仓储方法返回参数：Entity 类型、基本数据类型、DTO；

> 注：为何要分开仓储接口定义和仓储实现？
>
> 仓储的实现更多的涉及到基础设施层的东西（数据库等），故放在基础设施层；仓储接口的定义更关注输入输出，而这些跟领域密切相关，故放在领域层。这种用法是 DDD 推荐的方式，也是业界通行做法。
>
> 领域层仅仅依赖于仓储接口，不依赖于实现，这样我们可以调整实现而不影响领域层代码，比如我们可以调整依赖注入配置，将实现从 MySQL 改成 MongoDB，或者我们可以重构数据库结构，这些影响的都仅仅是仓储实现部分的代码。另外，领域层仅仅依赖于仓储接口，有利于单元测试，单元测试的时候，我们可以使用模拟的仓储类，从而不依赖于数据库等外设。



#### 定时任务

同 linux 的 Crontab。定时任务需要依赖 Redis（实现在分布式情况下仅有一台开启定时任务）。

- 类创建。在 app/Cron/ 下面创建定时任务处理程序类：

  ```php
  namespace App\Cron;
  
  use EasySwoole\EasySwoole\Crontab\AbstractCronTask;
  
  class Test extends AbstractCronTask
  {
      public static function getRule(): string
      {
          return '*/1 * * * *';
      }
  
      public static function getTaskName(): string
      {
          return 'test cron';
      }
  
      static function run(\swoole_server $server, int $taskId, int $fromWorkerId, $flags = null)
      {
          echo "test cron run logic\n";
      }
  }
  ```

- 在 config/cron.php 中配置：

  ```php
  return [
      // 定时任务项目名，同名的多台服务器只会有一台启动定时任务，请务必给不同项目起不同的名字，否则会相互影响
      'name' => 'user-center-platform',
      // crontab 需要 redis
      'redis' => 'main',
      'tasks' => [
          \App\Cron\Test::class
      ]
  ];
  ```

> 注意：定时任务同 Controller 一样也是**处理程序**，不能在里面直接写业务逻辑，业务逻辑同样需要在 Domain/ 中实现。



#### 异步任务

一些耗时的操作可以用异步任务后台处理。

-  定义：在 app/Tasks/ 下定义：

  ```php
  namespace App\Tasks;
  
  use App\Foundation\Mailer;
  use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;
  
  /**
   * 邮件发送异步任务
   * Class SendMail
   * @package App\Tasks
   */
  class SendMail extends AbstractAsyncTask
  {
      /**
       * @param array $taskData 数据：['host' => '', 'username' => '', 'password' => '', 	   'message'=> $object],其中 message 为 \Swift_Message类型，必填，其它选填
       * @param $taskId
       * @param $fromWorkerId
       * @param null $flags
       * @return mixed
       * @throws \Exception
       */
      protected function run($taskData, $taskId, $fromWorkerId, $flags = null)
      {
          if (!$taskData['message'] || !$taskData['message'] instanceof \Swift_Message) {
              throw new \Exception("邮件内容非法，必须为 \Swift_Message 类型");
          }
          $mailer = Mailer::getSwiftMailer($taskData['host'] ?? '', $taskData['username'] ?? '', $taskData['password'] ?? '');
          return $mailer->send($taskData['message']);
      }
  
      protected function finish($result, $task_id)
      {
          // nothing
      }
  }
  ```

- 投递：

  ```php
  use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
  ...
  TaskManager::async(new SendMail([...]));
  ```



#### 认证/鉴权

认证/鉴权工作应当放在路由层之前进行。

目前我们使用的自定义认证/鉴权方案不是太友好，要求请求数据必须全部放在 data 字段里面并 json encode，这对于 RESTful API 并不友好。

框架虽然提供了 ApiRoute 基类供需认证/鉴权路由继承，不过目前并没有实现认证/鉴权方案。后面可能会废弃掉应用层的认证，统一走应用网关认证，也有可能会实现一种认证（如 JWT 认证，由于目前旧系统并不支持，故尚未实现于框架中）。

另一种是如商户平台这种登录型系统，认证和鉴权是很明晰的（当然也是要放在路由层完成）。



#### 外部 API 调用

对外部系统的 api 调用主要是通过配置来实现的，最简单的只需要配置 server 和 path信息即可，复杂的可以配置请求协议（目前仅支持 http(s)）、请求参数组装器、服务器地址解析器、响应参数解析器、拦截器等，实现定制化调用。配置好后在程序中调用 `\WecarSwoole\Client\Client::call($apiName, $requestData)` 即可。

其中 $apiName 是 api 别名而不是 uri，这样做的好处是当需要修改 uri 时仅需修改配置文件即可。

api 是分组配置的，最佳实践是同一个接口提供方的 api 放在一组，而不同的提供方往往其请求参数组装方式、响应参数解析方式甚至是协议都不同，一般放到不同分组中。喂车内部接口（OS的例外）由于遵循相同的处理方式，可放到一组。 

1. 配置 Server。在 config/env/$env.php 中定义（实际中应该使用配置中心）：

```php
return [
    'server' => [
        'OL' => [
            'name' => '油号',
          	'app_id' => 2343,
          	'secret' => 'poukhsfdsasadf43423fddd2s2erldojf',
            'servers' => [
                ['url' => 'http://192.168.85.201:8081', 'weight' => 100],
            ],
        ],
    ]
];
```

2. 配置 api。在 config/api/ 中定义。如：

api.php:

```php
return [
    // 默认配置，如签名器、加密算法、数据格式等配置，这些配置都可以在各自 api 配置中覆盖（其中某些配置仅 http 协议适用）
    'config' => [
        // 请求协议
        'protocol' => 'http', // 支持的协议：http、rpc（尚未实现）
        // http 协议请求默认配置
        'http' => [
            // 服务器地址解析器，必须是 IHttpServerParser 类型
            'server_parser' => \App\Foundation\Client\Http\Component\DefaultHttpServerParser::class,
            // 请求参数组装器
            'request_assembler' => \App\Foundation\Client\Http\Component\DefaultHttpRequestAssembler::class,
            // 响应参数解析器
            'response_parser' => \App\Foundation\Client\Http\Component\JsonResponseParser::class,
            // 请求发送前的拦截器(尚未实现)
            'before_handle' => [],
            // 收到响应后的拦截器（尚未实现）
            'after_handle' => [],
            // https ssl 相关配置
            'ssl' => [
                // CA 文件路径
                'cafile' => '',
                // 是否验证服务器端证书
                'ssl_verify_peer' => false,
                // 是否允许自签名证书
                'ssl_allow_self_signed' => true
            ]
        ],
    ],
    // 组
    'wc' => include __DIR__ . '/weicheche.php',
];
```

weicheche.php:

```php
return [
    // 组公共配置，可覆盖 api.php 中的公共配置
    'config' => [
      	// 组 server 配置，底下 api 共用
				'server' => 'CP',
      	'http' => [

        ]
    ],
    // api 定义
    'api' => [
        'user.coupon.info' => [
            'desc' => '获取用户券信息',
            'server' => 'CP',// 可以是简写的，也可以是完整写法如 https://coupon.weicheche.cn,也可以是数组(从中取一个)
            // path 的格式支持占位符，如 users/{uid}，group/{?group_id} (?表示可选)，使用时根据传参替换
            'path' => '/usercoupons/getUserCoupons',
            'method' => 'POST', // http 协议下，请求方式
            'protocol' => 'http',
        ],
        'merchant.users.list' => [
            'desc' => '获取商户用户列表',
            'server' => 'UC',
            'path' => '/v1/merchants/{merchant}/users',
            'method' => 'GET',
        ],
        'users.add' => [
            'desc' => '添加用户',
            'server' => 'http://localhost:9501',
            'path' => '/v1/users',
            'method' => 'GET',
        ]
    ]
];
```

3. 调用：

```php
use WecarSwoole\Client\Client;
...
$result = Client::call('wc:users.add', $reqData);
var_export($result->getBody());
```

Client 目前仅支持 http 协议，但是可扩展的（比如支持 RPC 协议），api 具体用的什么协议是在配置文件中配置的，调用的时候不用管。

支持针对不同的分组或者单个 api 配置不同的请求参数组装器和响应参数解析器，这对于和第三方合作是很有用的，比如我们可以针对不同的第三方配置不同的分组，这些分组有各自的组装器和解析器实现。

系统对自身的 api 调用也需要在此处配置。

**有待实现：**

- 请求前后拦截器；
- 请求重试与告警机制；
- 支持异步（task投递）请求；

> 注：实际使用中，一般会将对外部系统的调用封装成服务类（Service），服务类调用具体接口并返回相应的数据（基本数据类型或者自定义类型）。



#### Exception

不同于之前的开发模式，我们采用行业通用的抛异常的方式代替返回错误码，在最外层捕获异常并记录日志，遵循"错误处理和业务逻辑分离"原则。

框架在 wecarswoole/Exceptions/ 中已经定义了一些异常，项目可以在自己的 Exceptions/ 目录中定义自己项目需要的异常类。



#### Email

使用 SwiftMail 扩展并进行相应封装，实现异步发送（投递 task 任务）。

**配置：**

config/config.php

```php
// 邮件。可以配多个
'mailer' => [
    'default' => [
        'host' => 'smtp.exmail.qq.com',
        'username' => 'robot@weicheche.cn',
        'password' => 'Chechewei123'
    ]
],
```

**使用：**

依赖注入加载：直接在构造函数中注入：

```php
use WecarSwoole\Mailer;
...
public function __construct(Mailer $mailer)
{
    $this->mailer = $mailer;
    parent::__construct();
}
```

直接创建（一般不推荐）：

`$mailer = new Mailer();`

(构造函数：`__construct(string $host = '', string $username = '', string $password = '')`)

发邮件：

```php
$message = new \Swift_Message("测试邮件", "<span style='color:red;'>邮件征文</span>");
$message->setFrom(['robot@weicheche.cn' => '喂车测试邮件'])->setTo('songlin.zhang@weicheche.cn')->setContentType('text/html');
$this->mailer->send($message);
```



#### Logger

框架没有使用 easyswoole 自带的 Logger（过于简单并不实用），使用遵循 PSR 规范的 monolog。同样，日志也是异步 task 处理的。

- 配置：

  config/env/$env.php 中配置开启级别，可配置 PSR 规定的所有级别（外加 off 关闭日志）：

  ```php
  // 最低记录级别：debug, info, warning, error, critical, off
  'log_level' => 'debug',
  ```

  config/config.php 中配置每个级别的 handler，目前支持的有 file 和 mailer，file 对应的配置日志文件名，mailer 对应的是邮件配置。如果某个级别没配置，则使用低级别的配置。可以配置多个 handler：

  ```php
  // 日志配置，可配置：file（后面对应目录），mailer（后面对应邮件配置）
  'logger' => [
      'debug' => [
          'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/debug_info.log'),
      ],
      'info' => [
          'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/debug_info.log'),
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
              'subject' => '喂车邮件告警',
              'to' => [
              ]
          ],
          'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/error.log'),
      ]
  ],
  ```

  可以配置 PSR 规定的所有级别。

- 使用：

  构造函数注入(已在 config/di/di.php 中配置了接口实现，或者使用 di 容器获取)：

  ```php
  use Psr\Log\LoggerInterface;
  ...
  public function __construct(LoggerInterface $logger) {
      $this->logger = $logger;
      parent::__construct();
  }
  ...
  $this->logger->critical("严重错误日志，需要发送邮件");
  ```



#### Cache

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



#### Redis

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



#### MySQL

使用 [dev/mysql](https://gitlab4.weicheche.cn/dev/mysql) 扩展。

一般情况下只在 `\WecarSwoole\Repository\MySQLRepository` 子类中使用，该类已经自动创建了 MySQL 实例，子类仅需要配置所使用的数据库别名即可。

1. 项目配置 config/env/$env.php

   ```php
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
   ```

2. 创建仓储继承基类：

   ```php
   use App\Domain\User\IUserRepository;
   use WecarSwoole\Repository\MySQLRepository;
   
   class MySQLUserRepository extends MySQLRepository implements IUserRepository
   {
   		/**
        * 添加用户
        * @param User $user
        * @return int|bool 成功返回 uid，失败返回 false
        */
       public function add(User $user)
       {
           $this->query->insert('users')->values([
               [
                   'name' => $user->name,
                   'phone' => $user->phone,
                   'nickname' => $user->nickname,
               ]
           ])->execute();
   
           return $this->query->lastInsertId();
       }
     
       /**
        * 根据 uid 获取用户
        * @param int $uid
        * @return User
        */
       public function getById(int $uid): ?User
       {
           $userInfo = $this->query->select('*')->from('users')->where(['uid' => $uid])->one();
   
           if ($userInfo) {
               $user = new User($userInfo['phone'], $userInfo['name'], $userInfo['nickname']);
               $user->setId($userInfo['uid']);
               return $user;
           }
   
           return null;
       }
     
       protected function dbAlias(): string
       {
           return 'user_center';
       }
   }
   ```

直接创建（不推荐）：

```php
use WecarSwoole\MySQLFactory;
...
$query = MySQLFactory::build('dbalias');
...
```



#### 事务

不要在**仓储**中使用事务（仓储要尽可能简单，引入事务会使仓储变得复杂而且很容易引入业务逻辑代码）。

不要在**控制器/handler** 中使用事务（原因同上）。

一般情况下**建议在 Service 中使用事务**（事务本身就有协调之含义。在 DDD 实践中，建议在应用服务中管理事务，不过我们为了使用上的简单性，没有引入应用服务的概念，感兴趣的同学可以自行百度了解）。

不推荐在 Entity 中使用事务，因为 Entity 需要保持类功能的单一性，引入事务往往会使一个 Entity 变得过于复杂，而且其它 Entity 或 Service 有可能调用此 Entity，会造成事务嵌套。

**事务不支持跨库。**

使用示例（以下仅作示例，并非最佳实践）：

```php
use WecarSwoole\Transaction;
...

$repos1 = Container::make(IUserRepository::class);
$repos2 = Container::make(IMerchantRepository::class);

$trans = Transaction::begin([$repos1, $repos2]);
$res1 = $repos1->add(new User('13909094444'));
$res2 = $repos2->add(new Merchant(29090, 1));

// 中间可以用 $trans->add($newRepos) 添加新仓储到事务中

if ($res1 && $res2) {
    $trans->commit();
} else {
    $trans->rollback();
}
```



#### DTO(data transfer object，数据传输对象)

DTO 并非严格意义上 OOD 中的对象，其起数据容器的作用，本质上属于数据结构范畴（《代码整洁之道》）。

DTO 在 OOD（特别是 DDD）中的一大作用是进行**用例层面**（和领域层面相对）的数据组装。

easySwoole 中的 Bean 实际上就是 DTO（不过很多人把它用作和数据库打交道的 Model 或者 DAO 了）。

一个事实是，**用例(用户)层面的东西和领域层面不是一一对应的**，比如说用例（用户）层面需要同时看到订单以及其下面的商品信息，甚至还包括这些商品的热度等，这些在用例（用户）层面属于一条查询"任务"，但在领域层面，它们属于不同的业务领域（订单领域和商品领域），一般会由不同的系统提供。通常，我们会通过一个聚合服务从多个子系统获取到相关信息，然后将这些信息聚合在一起，然后组装成一个符合用例要求的 DTO 返回到控制器，控制器进一步解析成客户端需要的数据格式（如 json）返回。

另外，一种典型情况是查询任务（列表查询或单条查询），这种查询没有什么业务逻辑，仅仅是从数据库取数据然后组装下返回给客户端。这种我们就没有必要使用 Domain 中的 Service、Entity 等这些重概念，可以在 Controller 中直接调用 Repository 获取数据，Repository 返回的也不是 Entity 对象，而是针对用例优化结构的 DTO 对象（用例查询优化）。如果这种查询很多，建议将查询的方法抽离成单独的 Repository（CQRS，[命令查询职责分离模式](https://www.cnblogs.com/yangecnu/p/Introduction-CQRS.html)）

另一种情况是，客户端传过来的数据（入参，上面提到的是出参）很多，如果我们一个个传参到 Service 中，Service 的构造函数参数会很长，一般我们也可以创建一个 DTO 来传参。

为何我们使用 DTO 而不是数组传递参数？

目的在于可维护性。数组过于灵活，数组的内容是不受约束的，而且也无法从参数直接知道具体有哪些内容。使用DTO的好处是明确传递的数据内容。另外由于 DTO 不受领域概念的约束，可以向应用层和基础设施层作亲和，例如可以在 DTO 对象中定义跟数据库表字段的数据转换规则等（而领域层的 Entity 则不应该知道应用层和存储层的细节，不能在 Entity 中作数据字段映射）。

DTO 应当放在哪？

从上面的分析可知，DTO 不属于 Domain，属于用例维度的东西，具体实现上一般用在 Controller/handler 和 Repository 中，建议可以根据使用情况放置，例如仓储返回的直接放在 Foudation/Repository/目录下。或者干脆直接在 app/下的 DTO/ 目录下（默认没有这个目录）亦可。

**待完善：**

目前框架尚未提供针对字段映射优化的 DTO，只能使用 easyswoole 的 Bean。后面会在此基础上做一个基类，增加字段映射功能（基于注解，但功能会弱于 ORM）。



####依赖注入

框架采用 PHP-DI 组件作为依赖注入容器。

建议项目中使用依赖注入解决依赖问题，具体的可以采用构造函数注入依赖、直接用容器 get/make。

- 构造函数注入：

  ```php
  public function __construct(CacheInterface $cache)
  {
      $this->cache = $cache;
      parent::__construct();
  }
  ```

- 容器获取：

  ```php
  use WecarSwoole\Container;
  ...
  Container::get(IUserRepository::class);
  ```

  `\WecarSwoole\Container` 是一个 Container facade 类：

  ```php
  /**
   * Container facade
   * Class Container
   * @package WecarSwoole
   */
  class Container
  {
      /**
       * 从容器获取对象。单例模式，只会实例化对象一次
       * @param $name
       * @return mixed
       */
      public static function get($name)
      {
          return Di::getInstance()->get("di-container")->get($name);
      }
  
      /**
       * 同 get，不过 make 每次会重新实例化对象
       * @param $name
       * @param array $parameters
       * @return mixed
       */
      public static function make($name, array $parameters = [])
      {
          return Di::getInstance()->get("di-container")->make($name, $parameters);
      }
  
      /**
       * 设置注入内容
       * @param $name
       * @param $value
       * @return mixed
       */
      public static function set($name, $value)
      {
          return Di::getInstance()->get("di-container")->set($name, $value);
      }
  
      public static function has($name)
      {
          return Di::getInstance()->get("di-container")->has($name);
      }
  }
  ```

**禁止在 Domain 中直接使用 Container 获取依赖（这样会造成 Domain 对 Container 的依赖），应当通过参数传递依赖**。

> 注意：easyswoole 属于常驻进程，除非重启，否则多次请求的 `$container` 都是同一个，因而 `$container->get("ClassName")` 在整个进程生命周期获取到的都是同一个对象实例，因而 `$container->get()` 只能用来获取单例（如 Cache、Logger 等）或者无状态对象，如果不然，则要用 `$container->make()`，否则会造成数据混乱。



### 框架提供的 Util 工具

除了 easyswoole 提供的一些工具以外，框架还提供了以下工具供使用：

- `\WecarSwoole\Util\AnnotationAnalyser`：注解分析器
  - `getPropertyAnnotations(string $className, array $annotationFilters = []):array`：获取类属性注解信息
- `\WecarSwoole\Util\File`：文件/目录操作工具，继承 `\EasySwoole\Utility\File`
  - `join(...$paths):string`：拼接文件名
  - … easyswoole 提供的功能
- `\WecarSwoole\Util\Url`：Url 辅助类
  - `assemble(string $uri, string $base = '', array $queryParams = [], array $flagParams = []): string`：组装 url
  - `parse(string $url): array`：解析出 schema,host,path,query_string



### 其它

##### 缺失的 Model

由于 Model 的具体含义有分歧，容易被乱用，实际中大部分时候被用作 ORM 和 DTO，并非真正意义上的"对象"（起的是数据结构作用），因而我们并没有引入 Model 的概念，而是引入 DDD 设计中的**仓储**概念。

##### 缺失的 Logic

Logic 一词同样含义模糊（一切皆逻辑），而且实际使用中过于扁平化，导致代码过于臃肿。取而代之，我们引入更加具有含义、更具纵深、更加灵活的 DDD 中的**Domain**概念（业务领域），让开发者根据实际情况自己组织代码层次。

##### 为何 Http/Controllers 下面有 V1 这样的版本划分？

Http/Controller 是系统最主要的对外 API，API 一旦定义则很难做不兼容修改（比如改个字段名，删掉某字段，改变字段含义等），因而可以采用版本控制，对外提供不同版本的 API(这也是为何 Restful API 的 uri 中包含版本的原因)。
不同于 API，Domain 不需要版本概念，因为 Web 产品一般只会演化而不会分版本（可能会划分新模块）。当然，会有定制化需求，可以采用其他方案处理。

##### 框架

一般不要用 `easyswoole stop force`，从其实现来看，会造成僵尸进程。

##### 工厂

- MySQL、Redis、Email、Cache、Logger 等基础设施都有相应工厂来创建，工厂依赖于 EasySwoole（主要依赖于配置），并且将具体的基础设施扩展与 EasySwoole 框架隔离（即扩展本身不依赖于框架）。
- 工厂返回的基础设施尽量符合 PSR 规范（如 Cache、Logger 等）。
- 虽然提供了工厂，但实际使用中不建议直接用工厂获取对象（工厂并不提供单例模式），项目中请用 IoC 注入（本项目用的是 PHP-DI，建议通过构造函数注入这些基础设施）。

##### 生产环境

每次发布生产后需执行：`composer dump-autoload -o` 优化自动加载速度。



### 待实现：

- webSocket 使用规范；

- 单元测试引入；

- 消息队列；

- 接口调用：智能决定重试次数、熔断等（后面视具体情况是否需要）；

- 服务健康状态监控；

- 请求执行日志；

- 异步事件订阅；