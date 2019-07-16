### 创建新项目

1. 创建项目目录 myproject;

2. cd myproject 并创建 `composer.json`，加入以下代码：

   ```json
   {
       "name": "wechar/wecarswoole_proj",
       "description": "your project name",
       "type": "project",
       "require": {
           "framework/wecarswoole": "^1.0"
       },
     	"require-dev": {
           "phpunit/phpunit": "^7.0",
           "phpunit/php-invoker": "*",
           "swoole/ide-helper": "dev-master"
       },
     	"autoload": {
           "psr-4": {
               "App\\": "app/",
               "Test\\": "tests/"
           }
       },
       "repositories": {
           "0": {
               "type": "vcs",
               "url": "https://gitlab4.weicheche.cn/framework/wecarswoole.git"
           },
           "1": {
               "type": "vcs",
               "url": "https://gitlab4.weicheche.cn/dev/locker.git"
           },
           "2": {
               "type": "vcs",
               "url": "https://gitlab4.weicheche.cn/dev/mysql.git"
           },
           "packagist": {
               "type": "composer",
               "url": "https://mirrors.aliyun.com/composer/"
           }
       }
   }
   ```
   
3. 执行 `composer install`

4. 执行 `php vendor/bin/wecarswoole install` 安装 WecarSwoole 框架

5. 修改配置文件（参见后面配置文件说明）

6. 启动：`php easyswoole start -d --env=dev` (—env : dev、test、preview、produce，-d 表示后台运行)

7. 以调试模式启动：`php easyswoole start --env=dev --debug`（调试模式下会打印所有的日志到屏幕）

8. 停止：`php easyswoole stop`

9. 其他指令参见 easyswoole 官网

> 生产环境请使用 `composer install --no-dev`，其它环境请使用 `composer install`，因为非生产环境以后可能会加单元测试流程。

**注意**

> 1. 由于我们目前没有私有 composer 仓库，故上面的配置文件采用 vcs 仓储模式加载组件，包括以后开发的新组建也要将 gitlab 地址加入到这里面（必须加入到项目的 composer.json 中，加入到下级组件的 composer.json 是无效的）；
> 2. 当搭建了私有 composer 仓库后，可以删掉这些 `vcs`  配置，只需将 `packagist` 项改成我们自己的私有仓库地址即可；
> 4. 当执行 composer 命令出错时（如 install、update 等），请在后面加 -vvv 查看详细信息（如 composer install -vvv）；
> 5. 项目不要提交 vendor 目录到 git 中；
> 6. 全局修改 composer 源：
>    1. 查看现在用的源：`composer config -lg`；
>    2. 修改源：`composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/`



### 在现有项目上开发

- 根据前面步骤创建项目并提交后，其他人 clone 下来执行 `composer install` 即可。
- 生产环境部署：部署平台（如 walle）需要增加指令：`composer install`，该指令会根据 composer.lock 文件信息安装指定版本的库。
- **不要在生产环境执行 `composer update`！**
- **不要每个开发人员随便在本地执行 `composer update`！**
- 一句话：**谨慎执行 `composer update`**，因为 composer update 指令会根据 composer.json 中的版本配置信息获取符合版本约束的最新代码并更新 composer.lock 文件，如果每个开发人员都去执行 composer update，那么 composer.lock 文件会频繁变动，造成不稳定，可能会出现莫名其妙的问题。



### 给项目引入新的包

1. 团队中某个成员在项目根目录下执行：`composer require vendor/package_name`，如 `composer require monolog/monolog ` ；
2. 提交到 gitlab；
3. 其他人 `git pull --rebase` 并执行 `composer install` 安装新的包；
4. 开发完成，发布；



### 更新包文件

1. 团队中某个成员在项目根目录下执行 `composer update vendor/package_name`，如 `composer update framework/wecarswoole`；
2. 提交到 gitlab；
3. 其他人 `git pull --rebase` 并执行 `composer install` 安装新的包；
4. 开发完成，发布；

> 注意：不要执行 `composer update` 一次更新所有包，要更新哪个就更新哪个。



### 移除不需要的包

两种方式：

- `composer remove $package-name`；
- 从 composer.json 中手动删除不需要的包，然后执行`composer update $package-name`；



### 更改命名空间

在 composer.json 中添加或修改了 autoload 项后，需执行 `composer dump-autoload` 更新自动加载；



### 更换国内镜像源

目前用的 composer 镜像是阿里云的，万一今后不可用，需要更换成其他镜像源，请按照以下步骤执行：

1. 修改项目根目录下的 composer.json 文件，更换源：

   ```json
   "repositories": {
     	...
       "packagist": {
           "type": "composer",
           "url": "https://mirrors.aliyun.com/composer/"
       }
   }
   ```

2. 执行命令更新 composer.lock 文件使用新源：`composer update nothing`



### 语义化版本控制

使用 composer 做依赖管理时（包括我们自己开发 composer 包），需要遵循语义化版本控制：

版本格式：**主版本号.次版本号.修订号**，版本号递增规则如下：

1. **主版本号**：当你做了**不兼容**的 API 修改；
2. **次版本号**：当你做了**向下兼容的功能性新增**；
3. **修订号**：当你做了**向下兼容的问题修正**；

更多信息请参见 [语义化版本控制](https://semver.org/lang/zh-CN/)

> 即是说，我们的包向外发布之后，不能随便修改其内容，一旦修改，就需要同时增加新的版本号（打 tag），版本号命名需遵循以上约束。



以上几点是 composer 的常见使用方式，大家记住最重要的一点：**谨慎执行任何导致 composer.lock 文件发生变化的操作指令（如update，require 等）**，因为一旦 composer.lock 发生变化并发布生产，生产环境将应用这些变化。


[返回](../README.md)