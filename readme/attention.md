### 注意

1. 项目根目录下的 dev.php 和 produce.php 是在 worker 进程启动前载入的，修改这些配置需要 stop & start 服务；
2. 项目 config/ 中的所有配置（除了自定义进程的配置如 cron.php、apollo.php）是在 worker/task 进程启动时载入的，这些配置修改仅需要 reload 服务；
3. 由于是在 worker 进程启动时载入的，config/ 下的配置在 worker 进程启动前无法直接使用，如果需要使用，必须使用 loadFile 的方式载入；
4. 重载服务必须用 `reload all` 而不是 `reload`，后者仅重启 task 进程；
5. `reload` 指令不会重启自定义进程，因而自定义进程的代码修改后必须 stop & start 服务；
6. 自定义进程中默认不可使用 config/ 下的大多数配置，也不可使用 Logger、Cache、依赖注入等，因为这些的初始化工作是发生在 worker/task 进程启动时的，如果要在自定义进程中使用，需要在自定义进程启动时执行 `\WecarSwoole\Bootstrap::boot()` 脚本；
7. 数据库、Redis 等公共资源必须从 apollo 配置中心获取，不可在代码写死；
8. `apollo()` 函数仅允许在 config/ 中使用，禁止在业务代码中调用此函数，业务代码中必须使用 EasySwoole 的 Config 获取（业务代码不应当关心配置来源）；
9. 所有的 woker/task 进程都调用了 `Runtime::enableCoroutine()` 将 PHP I/O 函数协程化，项目中使用 PHP 相关函数时需知晓；
10. 项目中涉及到目录的，必须使用 `EASYSWOOLE_ROOT`、`CONFIG_ROOT`、`STORAGE_ROOT` 这些基目录常量拼接，禁止使用诸如 `__DIR__` 这样的常量，因为目前 Walle 发布采用的是软连接模式，每次发布后实际的绝对路径都不一样，而每次发布后执行的是 `reload` 指令，如果在项目中使用 `__DIR__`，该变量的值还是旧目录，从而导致错误。
11. 禁止在 `Controller` 中使用私有属性、静态属性，必须使用 `protected`，因为 `Controller` 使用线程池，这些属性的生命周期是整个服务运行周期，会造成数据混乱。 