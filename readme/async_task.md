### 异步任务

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
