### 事务

一般情况下**建议在 Service 中使用事务**（事务本身就有协调之含义。在 DDD 实践中，建议在应用服务中管理事务，不过我们为了使用上的简单性，没有引入应用服务的概念，感兴趣的同学可以自行百度了解）。

不推荐在 Entity 中使用事务，因为 Entity 需要保持类功能的单一性，引入事务往往会使一个 Entity 变得过于复杂，而且其它 Entity 或 Service 有可能调用此 Entity，会造成事务嵌套。

**事务不支持跨库。**

使用示例（以下仅作示例，并非最佳实践）：

```php
use WecarSwoole\Transaction;
...

$repos1 = Container::get(IUserRepository::class);
$repos2 = Container::get(IMerchantRepository::class);

$trans = Transaction::begin([$repos1, $repos2]);
$res1 = $repos1->add(new User('13909094444'));
$res2 = $repos2->add(new Merchant(29090, 1));

// 中间可以用 $trans->add($newRepos) 添加新仓储到事务中

if ($res1 && $res2) {
    $trans->commit();
} else {
    $trans->rollback();
}
```

[返回](../README.md)
