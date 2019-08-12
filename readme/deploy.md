### 新项目部署

1. 创建新项目后，确保相关配置正确无误：
   1. apollo 配置中心正确创建了相应的项目（找 fw）；
   2. apollo 配置中心公共命名空间中为该项目配置了 modules 和 appid（fw.modules、fw.appids 命名空间下面，找fw）；
   3. apollo 配置中心公共命名空间中正确配置了该项目用到的数据库、Redis 等资源信息；
   4. 项目的 config/ 下面配置无误：
      1. config/apollo.php 正确配置了 app_id（1.1 创建的项目的 app_id）、namespaces（该项目需要监听的 namespace，至少需要监听 application、fw.appids、fw.modules 以及本项目需要用到的数据库、Redis 等公共资源的 namespace）；
      2. config/logger.php 中正确配置了邮件告警和短信告警的相关接收人；
      3. config/config.php 中正确配置了：
         - app_name
         - app_flag
         - app_id （app_flag、app_id 由 1.2 步骤配置的）
         - server （来自 fw.appids、fw.modules 命名空间）
         - mysql
         - redis （mysql 和 redis 的信息必须取 apollo 中的，不能直接在程序里面写死）
         - base_url
         - cache （目前非开发环境一般用的 redis）
         - log_level （一般开发环境 debug，其他环境 info）
2. 找运维搭建三个环境的服务器和 walle 发布：
   1. 服务器环境：
      1. PHP >= 7.2
      2. swoole >= 4.3.0
      3. predis 扩展
   2. 项目下的 storage/ 目录针对 www 用户（服务运行用户）需要有读写权限；
   3. 需要将 storage/apollo、storage/logs、storage/temp 目录软链接到外部，防止每次发版被覆盖；
3. 在项目外部创建一个 vendor 目录；
4. 每次发布前将外部的 vendor 目录（第 3 步创建的）拷贝到新项目根目录下，然后执行 `composer install`增量安装依赖；
5. 执行成功 `composer install ` 后将新项目下的 vendor/ 目录拷贝回覆盖外部的 vendor 目录（第 3 步创建的那个，保证外部的 vendor 是最新的）；
6. 发布；
7. 回滚：回滚时应当采用目录回滚，即直接将服务切换回旧版本目录，而不应该采用 walle 发布的方式，因为采用 walle 发布的方式，vendor 是无法回滚的，如果出现外部包的版本冲突，则会出问题；