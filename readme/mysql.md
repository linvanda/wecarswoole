### MySQL

使用 [dev/mysql](https://gitlab4.weicheche.cn/dev/mysql) 扩展。

一般情况下只在 `\WecarSwoole\Repository\MySQLRepository` 子类中使用，该类已经自动创建了 MySQL 实例，子类仅需要配置所使用的数据库别名即可。

1. 项目配置 config/env/$env.php

   ```php
   'mysql' => [
       'weicheche' => [
           // 读库使用二维数组配置，以支持多个读库
           'read' => [
               [
                   'host' => '192.168.85.135',
                   'port' => 3306,
                   'user' => 'root',
                   'password' => 'weicheche',
                   'database' => 'weicheche',
                   'charset' => 'utf8',
               ]
           ],
           // 仅支持一个写库
           'write' => [
               'host' => '192.168.85.135',
               'port' => 3306,
               'user' => 'root',
               'password' => 'weicheche',
               'database' => 'weicheche',
               'charset' => 'utf8',
           ],
           // 连接池配置
           'pool' => [
               'size' => 30
           ]
       ],
       // 可以不配置读写分离
       'user_center' => [
           'host' => '192.168.85.135',
           'port' => 3306,
           'user' => 'root',
           'password' => 'weicheche',
           'database' => 'user_center',
           'charset' => 'utf8',
           // 连接池配置
           'pool' => [
               'size' => 30
           ]
       ]
   ],
   ```

2. 创建仓储继承基类：

   ```php
   use App\Domain\User\IUserRepository;
   use WecarSwoole\Repository\MySQLRepository;
   
   class MySQLUserRepository extends MySQLRepository implements IUserRepository
   {
   		/**
        * 添加用户
        * @param User $user
        * @return int|bool 成功返回 uid，失败返回 false
        */
       public function add(User $user)
       {
           $this->query->insert('users')->values([
               [
                   'name' => $user->name,
                   'phone' => $user->phone,
                   'nickname' => $user->nickname,
               ]
           ])->execute();
   
           return $this->query->lastInsertId();
       }
     
       /**
        * 根据 uid 获取用户
        * @param int $uid
        * @return User
        */
       public function getById(int $uid): ?User
       {
           $userInfo = $this->query->select('*')->from('users')->where(['uid' => $uid])->one();
   
           if ($userInfo) {
               $user = new User($userInfo['phone'], $userInfo['name'], $userInfo['nickname']);
               $user->setId($userInfo['uid']);
               return $user;
           }
   
           return null;
       }
     
       protected function dbAlias(): string
       {
           return 'user_center';
       }
   }
   ```

直接创建（不推荐）：

```php
use WecarSwoole\MySQLFactory;
...
$query = MySQLFactory::build('dbalias');
...
```

[返回](../README.md)