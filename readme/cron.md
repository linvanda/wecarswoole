### 定时任务

同 linux 的 Crontab。

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
  
      // 注意：easyswoole 3.3.0 之前的版本后后面的版本此处的参数列表不一样，详情见官网。下面是 3.3.0 前的写法
      static function run(\swoole_server $server, int $taskId, int $fromWorkerId, $flags = null)
      {
          echo "test cron run logic\n";
      }
  }
  ```

- 在 config/cron.php 中配置：

  ```php
/**
 * 定时任务配置
 * 注意：目前这些配置不能通过 apollo() 从配置中心获取，因为一方面该配置是在服务启动前读取的，apollo() 函数
 * 尚未生效；另外更重要的，自定义进程无法通过 reload 重启，即配置中心修改配置后并不会生效
 */
return [
    // 只在这些服务器上执行 crontab。必须配置
    // 支持的格式：['192.168.0.23','172.16.0.31']，
    // 或者按照环境指定: ['dev' => '192.168.0.23', 'produce' => '120.25.216.158']
    // 注意这两种格式不兼容
    'ip' => ['dev' => '192.168.0.23'],
    'tasks' => []
];
  ```

> 注意：定时任务同 Controller 一样也是**处理程序**，不能在里面直接写业务逻辑，业务逻辑同样需要在 Domain/ 中实现。


[返回](../README.md)