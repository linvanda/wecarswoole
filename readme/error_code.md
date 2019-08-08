### 错误码

生成项目后在 app/ 下有个 ErrCode.php:

```php
namespace App;

use WecarSwoole\ErrCode as BaseErrCode;

/**
 * Class ErrCode
 * 200 表示 OK
 * 500 及以下为框架保留错误码，项目中不要用，项目中从 501 开始
 * @package App
 */
class ErrCode extends BaseErrCode
{
    // TODO 在此处定义自己项目的错误码
}
```

请在此类中定义错误码常量。

项目中应当总是使用错误码常量，不能直接写死数字。

错误码常量定义应当反映错误含义。