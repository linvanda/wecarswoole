### 目录结构

- project_root
  - app
    - Cron
    - Domain
      - Events
    - Exceptions
    - Foundation
      - Repository
      - Util
    - Http
      - Controllers
        - V1
          - $modules
      - Middlewares
      - Routes
    - Process
    - Subscribers
    - Tasks
  - config
    - api
    - di
    - env
    - subscriber
  - storage
    - app
    - cache
    - di
    - logs
    - temp
  - vendor
  - tests

#### 说明

app/ : 项目代码目录

app/Bootstrap : work/task 进程启动脚本，此脚本在 reload 服务后会执行

app/Cron/ : 定时任务

app/Domain/ : 业务（领域）逻辑，核心目录

app/Domain/Events/ : 领域事件

app/Exceptions/ : 异常类定义

app/Foundation/ : 基础设施（如仓储实现类等）

app/Foundation/Repository/: 仓储实现

app/Foundation/Util/: 工具

app/Http/ : http 路由、控制器，对外暴露 http api

app/Http/Controllers/: http 控制器

app/Http/Controllers/V1/: 版本

app/Http/Controllers/V1/$modules/: 模块划分（模块名具体而定）

app/Http/Middlewares/: 中间件（如路由中间件）

app/Http/Routes/: 路由定义

app/Process/ : 自定义进程

app/Subscribers/ : 事件订阅者

app/Tasks/ : 异步任务

config/ : 配置文件

config/api/ : 外部 api 定义

config/di/ : 依赖注入配置

config/env/ : 环境相关配置（如数据库、redis 等）

config/subscriber/ : 订阅者配置

storage/ : 存储相关（日志、缓存、临时文件等）

storage/app/ : 应用程序存储（如第三方证书等）

storage/cache/ : 文件缓存

storage/di/ : di 缓存

storage/logs/ : 文件日志

storage/temp/ : 临时文件

vendor/ : 第三方库

tests/ : 单元测试

mock/：(api请求)数据模拟

dev.php : 开发环境（包括开发、测试、预发布）swoole_server 配置

produce.php : 生产环境 swoole_server 配置

easyswoole : 服务启动/停止/重启脚本

EasySwooleEvent.php : 全局事件



> 注：以上目录划分确定了基本的开发规范，但实际开发过程中并不限制一定只能划分以上目录，各项目可以在此基础上根据实际需要开设额外的目录。
