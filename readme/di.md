### 依赖注入

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
