
### Exception

不同于之前的开发模式，我们采用行业通用的抛异常的方式代替返回错误码，在最外层捕获异常并记录日志，遵循"错误处理和业务逻辑分离"原则。

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

### 框架异常基类

框架提供了异常基类 `WecarSwoole\Exceptions`，扩展了以下功能：

- 创建异常时可通过构造函数提供 context、data、shouldRetry 参数，其中 context 会保存到日志中，data 会返回给客户端（对应 data 字段），shouldRetry 会返回客户端（对应 retry 字段，0/1，表示客户端是否需要重试）。

- 提供 shouldRetry()、withData(array $data)、withContext(array \$context) 方法供设置以上属性，这些方法可链式调用：

  ```php
  throw (new Exception("该手机号已经存在", 301))->withContext([...])->withData([...])->shouldRetry();
  ```
