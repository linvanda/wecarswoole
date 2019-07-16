### 单元测试

参见 [中文文档](https://phpunit.readthedocs.io/zh_CN/latest/)

### 引入单元测试库

框架已经默认为项目的 composer.json 加入了单元测试库，安装即可。

### 编写单元测试

- 在项目根目录下的 tests/ 目录下编写单元测试类；
- 单元测试类的目录层次和 app/ 中的相同（即 tests/ 对应 app/）;
- 单元测试类命名：className + Test 命名，如 User 类的测试类叫 UserTest；
- 测试类继承 `\PHPUnit\Framework\TestCase`；
- 测试方法统一叫 test*，方法名能反映测试目的，如 testAddUserWhenPhoneExists()；
- 单元测试详细写法参见官网文档；

### 执行单元测试

1. `cd $project_root_dir`;
2. `vendor/bin/phpunit -c phpunit.xml`