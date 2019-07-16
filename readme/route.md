### Http 路由

- 系统对外暴露的所有接口都要进行显式的路由定义；

- 定义文件：app/Http/Routes/ 中定义，如 User.php 定义用户相关路由；

- 基类：`\WecarSwoole\Http\Route`：

  ```php
  <?php
  
  namespace WecarSwoole\Http;
  
  /**
   * 路由基类
   * 中间件的注册方式：
   *  在子类的构造函数中调用 $this->appendMiddlewares(...)
   * 中间件执行顺序取决于注册顺序，类全局的先于特定路由的
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

- 框架提供了一个 `\WecarSwoole\Http\ApiRoute`基类，继承该类的路由都需走 api 鉴权（我们目前的鉴权方式）。

### 路由定义

使用 [fast-route](https://github.com/nikic/FastRoute) 规则。

```
/users
/users/{id:\d+}			-- 正则匹配：数字
/articles/{id:\d+}[/{title}]	-- 可选参数：title
```

### 路由中间件

可以添加中间件进行路由信息拦截，如用来做鉴权（api鉴权、登录验证等）。如果中间件抛出异常，则终止请求执行，返回错误给用户。

- 在 app/Http/Middlewares/ 中创建中间件类，需实现 `\WecarSwoole\Middleware\IRouteMiddleware` 接口（并实现其 `handle(Request $request)` 方法）;
- 在路由类的构造函数中调用 `$this->setMiddleware(array $middlewareNameList)` 给路由添加中间件，参数为中间件类名。该做法会让该路由类以及继承该路由类的路由全部应用该中间件；
- 还可以针对单独的路由添加中间件：在调用 get、post、put、delete 方法设置路由时第三个参数可以传入中间件列表，格式同上；

实践：设置两个路由指向同一个控制器，这两个路由一个暴露给公司内部，一个暴露给外部第三方，两者使用不同的鉴权机制，而实现的功能相同（因而使用同一个控制器）。可以创建两个路由父类，两者使用不同的鉴权中间件，一个对内，一个对外，所有内部 api 都继承对内的那个父类，对外 api 则继承另一个。

### RESTful API

建议使用 RESTful 风格 api 定义。关于 RESTful 请参见 [Restful API 最佳实践](http://www.ruanyifeng.com/blog/2018/10/restful-api-best-practices.html)

