### Email

使用 SwiftMail 扩展并进行相应封装，实现异步发送（投递 task 任务）。

**配置：**

config/config.php

```php
// 邮件。可以配多个
'mailer' => [
    'default' => [
        'host' => 'smtp.exmail.qq.com',
        'username' => 'robot@weicheche.cn',
        'password' => 'Chechewei123'
    ]
],
```

**使用：**

依赖注入加载：直接在构造函数中注入：

```php
use WecarSwoole\Mailer;
...
public function __construct(Mailer $mailer)
{
    $this->mailer = $mailer;
    parent::__construct();
}
```

直接创建（一般不推荐）：

`$mailer = new Mailer();`

(构造函数：`__construct(string $host = '', string $username = '', string $password = '')`)

发邮件：

```php
$message = new \Swift_Message("测试邮件", "<span style='color:red;'>邮件征文</span>");
$message->setFrom(['robot@weicheche.cn' => '喂车测试邮件'])->setTo('songlin.zhang@weicheche.cn')->setContentType('text/html');
$this->mailer->send($message);
```
