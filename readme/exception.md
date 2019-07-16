
### Exception

不同于之前的开发模式，我们采用行业通用的抛异常的方式代替返回错误码，在最外层捕获异常并记录日志（基类控制器已自动捕获，业务控制器无需处理），遵循"错误处理和业务逻辑分离"原则。

框架在 wecarswoole/Exceptions/ 中已经定义了一些异常，项目可以在自己的 Exceptions/ 目录中定义自己项目需要的异常类。

框架提供的异常类：

- Exception 异常基类
- AuthException	授权异常
- ConfigNotFoundException 配置信息获取失败
- InvalidOperationException 非法操作
- MethodNotFoundException 方法不存在
- ParamsCannotBeNullException 参数不能为空
- PropertyCannotBeNullException 属性不能空
- PropertyNotFoundException 属性不存在
- CriticalErrorException 严重异常。如果用的框架默认配置，该异常会记录文件日志并发送邮件告警
- EmergencyErrorException 比 CriticalErrorException 还严重的异常。如果用的框架默认配置，该异常会记录文件日志并发送邮件和短信告警
- ValidateException 数据验证异常
- ...

### 框架异常基类

框架提供了异常基类 `WecarSwoole\Exceptions`，扩展了以下功能：

- 创建异常时可通过构造函数提供 context、data、shouldRetry 参数，其中 context 会保存到日志中，data 会返回给客户端（对应 data 字段），shouldRetry 会返回客户端（对应 retry 字段，0/1，表示客户端是否需要重试）。

- 提供 shouldRetry()、withData(array $data)、withContext(array \$context) 方法供设置以上属性，这些方法可链式调用：

  ```php
  throw (new Exception("该手机号已经存在", 301))->withContext([...])->withData([...])->shouldRetry();
  ```



### 异常与日志的结合

任何层抛出异常，如果中间没有被捕获，最终到达 Controller 层都会被捕获并记录日志，并给予调用方 json 格式的错误提示。

抛异常时请不要抛 PHP 基类 `\Exception`，而是抛框架基类 `WecarSwoole\Exceptions\Exception`，该基类做了相关扩展。

如果抛出 `CriticalErrorException`，则根据框架默认实现，会记录文件日志、发送邮件告警。

如果抛出 `EmergencyErrorException`，根据框架默认实现，会记录文件日志、发送邮件和短信告警。邮件和短信接收方在 config/logger.php 中配置。

抛出异常时，除了给予客户端相关文本提示，还可以携带额外 data 数据，通过 Exception::withData() 实现。（如添加用户时，由于用户已经存在而抛出异常，此时客户端可能想知道已存在的用户信息，进一步提示用户是否走合并流程）。

抛异常时，可以记录额外信息到日志，通过 Exception::withContext() 实现。

抛异常时，可以告诉客户端是否需要重试，通过 Exception::shouldRetry() 实现，默认不重试。比如由于当前系统调用第三方系统超时导致异常，我们希望调用方重新发起调用。

当抛出的是数据库异常，会为客户端屏蔽敏感信息。



[返回](../README.md)