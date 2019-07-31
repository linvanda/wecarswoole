### 注意

1. 项目根目录下的 dev.php 和 produce.php 是在 worker 进程启动前载入的，修改这些配置需要 stop & start 服务；
2. 项目 config/ 中的所有配置（除了自定义进程的配置如 cron.php、apollo.php）是在 worker/task 进程启动时载入的，这些配置修改仅需要 reload 服务；
3. 由于是在 worker 进程启动时载入的，config/ 下的配置在 worker 进程启动前无法直接使用，如果需要使用，必须使用 loadFile 的方式载入；
4. 重载服务必须用 `reload all` 而不是 `reload`，后者仅重启 task 进程；
5. `reload` 指令不会重启自定义进程，因而自定义进程的代码修改后必须 stop & start 服务；
6. 自定义进程中默认不可使用 config/ 下的大多数配置，也不可使用 Logger、Cache、依赖注入等，因为这些的初始化工作是发生在 worker/task 进程启动时的，如果要在自定义进程中使用，需要在自定义进程启动时执行 `\WecarSwoole\Bootstrap::boot()` 脚本；
7. 数据库、Redis 等公共资源必须从 apollo 配置中心获取，不可在代码写死；
8. 所有的 woker/task 进程都调用了 `Runtime::enableCoroutine()` 将 PHP I/O 函数协程化，项目中使用 PHP 相关函数时需知晓；