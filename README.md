WecarSwoole
----

### 简介
WecarSwoole 是基于 EasySwoole 开发的适用于喂车业务系统的 Web 开发框架。
[EasySwoole 使用说明](http://www.easyswoole.com)

**开发前请务必先看完本文档，特别是[注意点](./readme/attention.md)，以及启动、部署相关。**

### 环境要求
- PHP >= 7.2
- Swoole >= 4.3.0
- phpredis 扩展
  
### 系统设计要点

- 可扩展性
- 容易和第三方系统对接
- 可测试
- 遵循 [PSR 规范](https://www.php-fig.org)
- 组合优于继承：
  - 类继承层次不要过深，一般不要超过3层。
  - 不要在基类写太多功能，基类功能越多越笨重不灵活。
  - 优先使用多个类组合完成功能，而不是全塞到基类里面实现。

### 目录

- [启动项目](./readme/creat_project.md)
- [新项目部署](./readme/deploy.md)
- [注意点](./readme/attention.md)
- [目录结构](./readme/dir.md)
- [分层模型](./readme/layer.md)
- [配置](./readme/config.md)
- [错误码](./readme/error_code.md)
- [路由](./readme/route.md)
- [控制器](./readme/controller.md)
- 领域
    - [领域事件](./readme/event.md)
    
    - [仓储](./readme/repos.md)
    
    - [实体](./readme/entity.md)
- [定时任务](./readme/cron.md)
- [异步任务](./readme/async_task.md)
- [API 调用](./readme/invoke.md)
- [RequestId](./readme/request_id.md)
- [Exception](./readme/exception.md)
- [中间件](./readme/middleware.md)
- 基础设施
    - [Email](./readme/email.md)
    
    - [Logger](./readme/logger.md)

    - [Cache](./readme/cache.md)

    - [Redis](./readme/redis.md)

    - [MySQL](./readme/mysql.md)
- [事务](./readme/trans.md)
- [DTO](./readme/dto.md)
- [依赖注入](./readme/di.md)
- [Util 工具](./readme/util.md)
- 测试
    - [单元测试](./readme/union_test.md)
- [代码规范](./readme/code_rule.md)
- [杂项](./readme/others.md)