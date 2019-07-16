### 代码规范

代码需符合 [PHP 开发规范](https://github.com/linvanda/think/blob/master/PHP编码规范.md)。

##### phpcs (PHP Code Sniffer)：

phpcs 用来检测代码编写规范（如是否符合 PSR-2 规范）。

安装：

1. composer 全局安装 [phpcs]((https://github.com/squizlabs/PHP_CodeSniffer))：`composer global require "squizlabs/php_codesniffer=*"`（注意观察输出，告知了  phpcs 安装目录，Windows 应该是C:Users/Administrator/AppData/Roaming/Composer/vendor/bin 里面的 phpcs.bat，后面需要用到）；
2. [PhpStorm 集成 phpcs](https://www.jetbrains.com/help/phpstorm/using-php-code-sniffer.html)：
   1. 打开 phpstorm 点击 File->Settings->Languages & Frameworks->PHP->Code Sniffer，点击 Configuration 右侧的按钮，选择 PHP Code Sniffer (phpcs) path: 的路径，就是刚才 composer 之后生成的那个 phpcs.bat的路径。选择之后点击 Validate 验证成功；
   2. 点击 Editor->Inspections，展开点击右侧的 PHP，勾选 PHP Code Sniffer Validation，Coding Standard 选择右侧的 PSR12；
   3. 如果写的代码不符合 PSR-12 编码风格规范的时候，该行代码会有波浪线，点击波浪线可以查看提示信息，根据信息我们修改就可以写出优雅的代码了。

**phpcbf (PHP Code Beautify Fixer):**

phpcbf 和 phpcs 配合使用，用来将 phpcs 检测出来的问题代码一键格式化为符合规范的代码。

上面 composer 安装 phpcs 过程中已经自动安装了 phpcbf。

手动用法：

1. `phpcs $fileNameOrDirName` 检测代码看有哪些规范问题；
2. `phpcbf $fileNameOrDirName` 格式化代码；

##### phpmd (PHP Mess Detector):

phpmd 用来检测代码坏味道(如类大小、命名规范等)。

安装：

1. composer 全局安装 [phpmd](https://github.com/phpmd/phpmd)：`composer global require phpmd/phpmd`；
2. [PhpStorm 集成 phpmd](https://www.jetbrains.com/help/phpstorm/using-php-mess-detector.html)：
   1. 打开 phpstorm 点击 File->Settings->Languages & Frameworks->PHP->Mess Detector，点击 Configuration 右侧的按钮，选择 PHP Mess Detector (phpmd) path: 的路径，就是刚才 composer 之后生成的那个 phpmd.bat的路径。选择之后点击 Validate 验证成功；
   2. 点击 Editor->Inspections，展开点击右侧的 PHP，勾选 PHP Mess Detector Validation，将相关 options 都选上，确定；
   3. 当写的代码不符合 phpmd 规范，则会有波浪线提示。


[返回](../README.md)