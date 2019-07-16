### 中间件

框架广泛使用中间件的方式实现可扩展性。

中间件使用观察者模式实现业务逻辑解耦与可扩展性。

任何类通过使用 trait `WecarSwoole\Middleware\MiddlewareHelper` 都可以拥有添加、执行中间件的能力。

中间件并没有统一接口，各处根据需求定义自己的中间件接口（如 IRouteMiddleware、IControllerMiddleware 等）。

框架提供了一个可选的基类 `WecarSwoole\Middleware\Middleware`，该基类实现了 proxy 功能：中间件内部可以通过 `$this->proxy->methodName(...)`访问通过构造函数传入的对象（宿主对象）的受保护的方法和属性（类似于友元），如 IControllerMiddleware 相关中间件的实现：

```php
/**
 * 验证器中间件
 * Class ValidateMiddleware
 * @package WecarSwoole\Http\Middlewares
 */
class ValidateMiddleware extends Middleware implements IControllerMiddleware
{
    public function __construct(Controller $controller)
    {
        parent::__construct($controller);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return bool|mixed
     * @throws ValidateException
     */
    public function before(Next $next, Request $request, Response $response)
    {
        if (!($rules = $this->proxy->validateRules())) {
            goto last;
        }

        $action = basename(explode('?', $request->getRequestTarget())[0]);
        if (!array_key_exists($action, $rules)) {
            goto last;
        }

        $validate = new Validate();
        foreach ($this->formatRules($rules[$action]) as $paramName => $paramRules) {
            $ruleObj = $validate->addColumn($paramName);
            // 添加该字段的规则
            foreach ($paramRules as $ruleName => $ruleOpts) {
                if (method_exists($ruleObj, $ruleName)) {
                    $ruleObj->{$ruleName}(...$ruleOpts);
                }
            }
        }

        // 执行验证
        if (!$this->proxy->validate($validate)) {
            throw new ValidateException($validate->getError()->getErrorRuleMsg());
        }

        last:
        return $next($request, $response);
    }
  
    ...
```

这里作出的设计折中是：通过中间件反模式的设计（访问其他对象的受保护方法），避免控制器将 validateRules() 声明为 public。由于中间件和宿主（这里的 Controller）是紧密关联的， 这种折中是可接受的。

如果你的中间件不需要访问宿主对象的内部信息，则不需要继承该基类。



### 定义或制作自己的业务中间件

1. 定义中间件接口，如上面的 IControllerMiddleware;
2. 编写中间件实现类；
3. 相关类 `use MiddlewareHelper`;
4. 在类构造函数（或者通过配置文件注入）注入中间件：`$this->appendMiddlewares($middlewares)`；
5. 在相关节点执行中间件的方法，如`$this->execMiddlewares('before', $request, $response)`；



[返回](../README.md)