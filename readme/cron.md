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
      'redis' => 'main',
      'tasks' => [
          \App\Cron\Test::class
      ]
  ];
  ```

> 注意：定时任务同 Controller 一样也是**处理程序**，不能在里面直接写业务逻辑，业务逻辑同样需要在 Domain/ 中实现。
