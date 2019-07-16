### 仓储

仓储是领域对象（实体）和存储设施（如 MySQL 数据库）之间的桥梁，它知道两方面内容：领域对象属性细节和存储细节。

行业实践上，分成仓储接口和仓储实现，在 Domain/ 中定义仓储接口（如 `IUserRepository`），在 Foundation/Repository/ 中定义具体实现（如 `MySQLUserRepository`）。Domain/ 中只依赖于接口，不依赖实现，这样好处是后面可以随意更改实现（如换成 MongoDB）。

框架默认使用的是 MySQL 实现，在 `config/di.php` 中定义：   `'App\Domain\*\I*Repository' => \DI\create('\App\Foundation\Repository\*\MySQL*Repository')`，这里要求接口所在的目录结构和Foundation/Repository/ 目录结构一致，且命名需符合规范（将 I 替换成 MySQL，其它不变）。如果需要更改实现，需在此处配置（注意放到这条之前，否则不会用到。具体参见 [PHP-DI](http://php-di.org)）。

**仓储里面不要写业务逻辑，仅仅实现存储**。如不应该在仓储里面进行用户存在性判断。

**MySQL 版仓储不允许跨库。**

- 仓储接口定义：一般直接放在 app/Domain/$module/ 下面（对于复杂的模块也可以定义专门子目录）：

  ```php
  interface IUserRepository
  {
      /**
       * 添加用户
       * @param User $user
       * @return int|bool 成功返回 uid，失败返回 false
       */
      public function add(User $user);
  
      /**
       * 根据 uid 获取用户
       * @param int $uid
       * @return User
       */
      public function getById(int $uid): ?User;
  }
  ```

- 仓储实现：一般放在 app/Foundation/Repository/$module/ 下面（对应上面的目录结构）：

  ```php
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
       * @throws \App\Exceptions\PropertyNotFoundException
       * @throws \App\Exceptions\InvalidOperationException
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
  }
  ```

  


> 注：为何要分开仓储接口定义和仓储实现？
>
> 仓储的实现更多的涉及到基础设施层的东西（数据库等），故放在基础设施层；仓储接口的定义更关注输入输出，而这些跟领域密切相关，故放在领域层。这种用法是 DDD 推荐的方式，也是业界通行做法。
>
> 领域层仅仅依赖于仓储接口，不依赖于实现，这样我们可以调整实现而不影响领域层代码，比如我们可以调整依赖注入配置，将实现从 MySQL 改成 MongoDB，或者我们可以重构数据库结构，这些影响的都仅仅是仓储实现部分的代码。另外，领域层仅仅依赖于仓储接口，有利于单元测试，单元测试的时候，我们可以使用模拟的仓储类，从而不依赖于数据库等外设。
