### 领域事件

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


[返回](../README.md)