### Logger

框架没有使用 easyswoole 自带的 Logger（过于简单并不实用），使用遵循 PSR 规范的 monolog。同样，日志也是异步 task 处理的。

- 配置：

  config.php 中配置开启级别，可配置 PSR 规定的所有级别（外加 off 关闭日志）：

  ```php
  // 最低记录级别：debug, info, warning, error, critical, off
  'log_level' => apollo('application', 'log_level') ?: 'info',
  ```

  config/logger.php 中配置每个级别的 handler，目前支持的有 file、mailer、sms，file 对应的配置日志文件名，mailer 对应的是邮件配置。如果某个级别没配置，则使用低级别的配置。可以配置多个 handler：

  ```php
  // 日志配置，可配置：file（后面对应目录），mailer（后面对应邮件配置）、sms
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
              'to' => json_decode(apollo('application', 'logger.emails'), true) ?: []
          ],
          'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/error.log'),
      ],
    	    'emergency' => [
          'mailer' => [
              'driver' => 'default',
              'subject' => '喂车告警',
              'to' => json_decode(apollo('application', 'logger.emails'), true) ?: []
          ],
          'file' => File::join(EASYSWOOLE_ROOT, 'storage/logs/error.log'),
          'sms' => json_decode(apollo('application', 'logger.mobiles'), true) ?: []
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

> 根据框架默认配置，开发环境开启 debug 级别日志，生产环境开启 error 级别日志。
> 所有 error 及以下的日志都是记录文件，critical 记录文件并发邮件，emergency 记录文件并发送邮件和短信。
>
> 在项目中抛出 CriticalErrorException 会记录 critical 级别日志，抛出 EmergencyErrorException 会记录 emergency 级别日志。


[返回](../README.md)