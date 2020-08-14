### Http 路由

- 系统对外暴露的所有接口都要进行显式的路由定义；

- 定义文件：app/Http/Routes/ 中定义(可多个文件定义，文件名自定义)，如 User.php 定义用户相关路由；

- 基类：`\WecarSwoole\Http\Route`：

  ```php
  <?php
  
  namespace WecarSwoole\Http;
  
  /**
   * 路由基类
   * 中间件的注册方式：在子类的构造函数中调用 $this->appendMiddlewares(...)
   */
  abstract class Route
  {
      use MiddlewareHelper;
    
    	...
  
      public function get(string $routePattern, string $handler)
      {
          ...
      }
  
      public function post(string $routePattern, string $handler)
      {
          ...
      }
  
      public function put(string $routePattern, string $handler)
      {
          ...
      }
  
      public function delete(string $routePattern, string $handler)
      {
          ...
      }
    
    	...
  
      /**
       * 子类在此处添加路由
       * @return mixed
       */
      abstract function map();
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

- 框架提供了一个 `\WecarSwoole\Http\ApiRoute`基类，继承该类的路由都需走 api 鉴权（喂车内部的 token 鉴权机制）。为了开发人员临时调试问题（如调试接口是否可用），框架提供了越权能力，在 config.php 文件中配置 auth_request = 0 可以临时禁用 ApiRoute 中的 token 鉴权，一般将该配置放在配置中心，这样不用改代码就可以临时禁用生产环境的 token 鉴权（安全起见，请务必在调试完成后将鉴权加回去）。

### 路由定义

使用 [fast-route](https://github.com/nikic/FastRoute) 规则。

```
/users
/users/{id:\d+}			-- 正则匹配：数字
/articles/{id:\d+}[/{title}]	-- 可选参数：title
```

### 路由中间件

可以添加中间件进行路由信息拦截，如用来做鉴权（api鉴权、登录验证等）。如果中间件抛出异常，则终止请求执行，返回错误给用户。

1. 在 app/Http/Middlewares/ 中创建中间件类，需实现 `\WecarSwoole\Middleware\IRouteMiddleware` 接口（并实现其 `handle(Request $request)` 方法）;

2. 在路由类的构造函数中调用 `$this->setMiddlewares(array $middlewares)` 或者 `$this->appendMiddlewares($middlewares)` 给路由添加中间件，参数为中间件类名或中间件实例。如此该路由类以及继承该路由类的路由全部应用这些中间件；

实践：设置两个路由指向同一个控制器，这两个路由一个暴露给公司内部，一个暴露给外部第三方，两者使用不同的鉴权机制，而实现的功能相同（因而使用同一个控制器）。可以创建两个路由父类，两者使用不同的鉴权中间件，一个对内，一个对外，所有内部 api 都继承对内的那个父类，对外 api 则继承另一个。

### RESTful API

支持 RESTful 风格 API 书写。


[返回](../README.md)