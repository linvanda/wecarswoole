### 控制器

目录：`app/Http/Controllers/$version`。

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

### 控制器中间件

可以在控制器中调用 setMiddlewares 或者 appendMiddlewares 设置控制器中间件，中间件需实现 `\WecarSwoole\Middleware\IControllerMiddleware` 接口。

框架提供的中间件：

- `RequestRecordMiddleware`：记录请求信息。
- `LockerMiddleware`：加并发锁。
- `RequestTimeMiddleware`：请求超时告警中间件。
- `ValidateMiddleware`：验证器。

### 请求并发锁：

为了应对同一个请求在短时间内异常多次请求，造成数据错误，可以使用并发锁中间件`LockerMiddleware`。

1. 在配置文件中配置：

   ```php
   'concurrent_locker' => [
       'onoff' => 'on',
       'redis' => 'main'
   ],
   ```

2. 基类已经添加了此中间件，增加以上配置后，即自动使用并发锁。

3. 自定义锁：

   在业务控制器：

   ```php
       /**
        * 并发锁定义，定义用哪些请求信息生成锁的 key。默认采用"客户端 ip + 请求url + 请求数据"生成 key
        * 格式：[请求action=>[请求字段数组]]。
        * 注意，如果提供了该方法，默认是严格按照该方法的定义实现锁的，即如果请求action没有出现在该方法中，就不会加锁，
        * 除非加上 '__default' => 'default'，表示如果没有出现在该方法中，就使用默认策略加锁（客户端 ip + 请求url + 请求数据）。
        * 例：
        * // 只有 addUser 会加锁：
        * [
        *      'addUser' => ['phone', 'parter_id']
        * ]
        * // addUser 按照指定策略加锁，其它 action 按照默认策略加锁
        * [
        *      'addUser' => ['phone', 'parter_id'],
        *      '__default' => 'default'
        * ]
        * // addUser 不加锁，其它按照默认策略加锁
        * [
        *      'addUser' => 'none',
        *      '__default' => 'default'
        * ]
        */
       protected function lockerRules(): array
       {
           return [
             	'info' => ['phone'],
               '__default' => 'default'
           ];
       }
   ```

### 验证器：

基类已经添加了验证器中间件，业务控制器增加以下代码使用验证器：

```php
    /**
     * 验证器规则定义
     * 格式同 easyswoole 的格式定义，如
     * [
     *      // action
     *      'addUser' => [
     *          // param-name => rules
     *          'user_flag' => ['alpha', 'between' => [10, 20], 'length' => ['arg' => 12, 'msg' => '长度必须为12位']],
     *       ],
     * ]
     * 即：
     *      如果仅提供了字符串型（key是整型），则认为 arg 和 msg 都是空
     *      如果提供了整型下表数组，则认为改数组是 arg，msg 为空
     *      完全形式是如上面 length 的定义
     *
     * @see http://www.easyswoole.com/Manual/3.x/Cn/_book/Components/validate.html
     * @return array
     */
    protected function validateRules(): array
    {
        return [
            'info' => [
                'user_flag' => ['required'],
                'flag_type' => ['required', 'integer'],
              	'partner_type' => ['integer', 'optional'],// optional 表示该字段可选
            ]
        ];
    }
```



> 注：不建议在控制器中进行鉴权（如 api 鉴权、登录验证等），因为这样的话控制器就只能局限于当前鉴权上下文使用（如只能在用户登录状态下使用）。建议将鉴权操作前置到路由层（通过路有中间件实现，这点同 Laravel），路由层如果鉴权通过后，将必要信息追加到请求参数中传递给控制器。
