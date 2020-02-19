### 仓储

仓储是领域对象（实体）和存储设施（如 MySQL 数据库）之间的桥梁，它知道两方面内容：领域对象内部细节和存储细节。

行业实践上，仓储接口和仓储实现分开放置，在 Domain/ 中定义仓储接口（如 `IUserRepository`），在 Foundation/Repository/ 中定义具体实现（如 `MySQLUserRepository`）。Domain/ 中只依赖于接口，不依赖实现，这样好处是后面可以随意更改实现（如换成 MongoDB）。

框架默认使用的是 MySQL 实现，在 `config/di.php` 中定义：   `'App\Domain\*\I*Repository' => \DI\create('\App\Foundation\Repository\*\MySQL*Repository')`，这里要求接口所在的目录结构和Foundation/Repository/ 目录结构一致，且命名需符合规范（将 I 替换成 MySQL，其它不变）。如果需要更改实现，需在此处配置（注意放到这条之前，否则不会用到。具体参见 [PHP-DI](http://php-di.org)）。

**仓储里面不要写业务逻辑，仅仅实现存储**。如不应该在仓储里面进行用户存在性判断。

**MySQL 版仓储不允许跨库。**

- 仓储接口定义：一般直接放在 app/Domain/$module/ 下面（对于复杂的模块也可以定义专门子目录）：

  ```php
  <?php
  
  namespace App\Domain\User;
  
  use App\DTO\User\UserDTO;
  
  /**
   * 用户聚合仓储
   * Interface IUserRepository
   * @package App\Domain\User
   */
  interface IUserRepository
  {
      /**
       * 添加用户
       * @param User $user
       * @return User 新增的用户
       */
      public function add(User $user): User;
  
      /**
       * 根据 UserId 获取用户信息
       * @param UserId $userId
       * @return UserDTO
       */
      public function getDTOByUserId(UserId $userId): ?UserDTO;
  
      public function getUserByPartner(?Partner $partner): ?User;
  
      public function getUserByPhone($phone = ''): ?User;
  
      public function getUserByUid(int $uid): ?User;
  
      public function update(User $user, User $oldUser = null);
  
      public function merge(User $targetUser, User $abandonUser);
  
      public function isPhoneBeUsed($phone): bool;
  }
  
  ```

- 仓储实现：一般放在 app/Foundation/Repository/$module/ 下面（对应上面的目录结构）：

  ```php
  <?php
  
  namespace App\Foundation\Repository\User;
  
  use App\Domain\User\Partner;
  use App\Domain\User\User;
  use App\Domain\User\IUserRepository;
  use App\Domain\User\UserId;
  use App\DTO\User\UserDTO;
  use App\Foundation\Repository\MySQLUserCenterRepository;
  use Psr\SimpleCache\CacheInterface;
  use Swoole\Exception;
  use WecarSwoole\Exceptions\InvalidOperationException;
  
  /**
   * MySQL 版仓储实现
   */
  class MySQLUserRepository extends MySQLUserCenterRepository implements IUserRepository
  {
      private $cache;
  
      /**
       * MySQLUserRepository constructor.
       * @param CacheInterface $cache
       * @throws \Exception
       */
      public function __construct(CacheInterface $cache)
      {
          $this->cache = $cache;
  
          parent::__construct();
      }
  
      /**
       * 添加用户
       * @param User $user
       * @return User 添加的 User
       * @throws Exception
       * @throws \Exception
       * @throws \WecarSwoole\Exceptions\InvalidOperationException
       */
    public function add(User $user): User
      {
          $userData = [
              'nickname' => $user->nickname,
              'phone' => $user->phone(),
              'name' => $user->name,
              'gender' => $user->gender,
              'birthday' => $user->birthday,
              'headurl' => $user->headurl,
              'tinyheadurl' => $user->tinyheadurl,
              'regtime' => $user->regtime,
              'channel' => $user->registerFrom,
              'invite_code' => $user->inviteCode,
              'update_time' => time(),
          ];
  
          // 微信大号
          if ($wxPartner = $user->getPartner(Partner::P_WEIXIN)) {
              $userData['wechat_openid'] = $wxPartner->userId();
          }
  
          // 添加到主表
          $this->query->insert('wei_users')->values($userData)->execute();
  
          if (!($uid = $this->query->lastInsertId())) {
              throw new Exception("添加用户失败");
          }
  
          // 支付宝大号
          if ($alipayPartner = $user->getPartner(Partner::P_ALIPAY)) {
              $this->query->insert('wei_auth_users')
                  ->values([
                      'type' => 1,
                      'uid' => $uid,
                      'user_id' => $alipayPartner->userId(),
                      'create_time' => time(),
                      'update_time' => time()
                  ])->execute();
          }
  
          // TODO 车牌号目前没有地方记录
  
          $user->setUid($uid);
  
          return $user;
      }
    
    	...
  }
  ```
  
> 注：为何要分开仓储接口定义和仓储实现？
>
> 仓储的实现更多的涉及到基础设施层的东西（数据库等），故放在基础设施层；仓储接口的定义关注业务对象的获取，而这些跟领域密切相关，故放在领域层。这种用法是 DDD 推荐的方式，也是业界通行做法。
>
> 领域层仅仅依赖于仓储接口，不依赖于实现，这样我们可以调整实现而不影响领域层代码，比如我们可以调整依赖注入配置，将实现从 MySQL 改成 MongoDB，或者我们可以重构数据库结构，这些影响的都仅仅是仓储实现部分的代码。另外，领域层仅仅依赖于仓储接口，有利于单元测试，单元测试的时候，我们可以用Stub 模拟仓储实现，从而不依赖于数据库等外设。

#### 仓储模式一些难点解决：
仓储在存储领域对象的时候，可能需要获取对象的私有属性以存储起来，并在获取对象的时候将私有属性设置回去，而 PHP 是不允许外部类访问私有属性的。
我们的解决方案是在 `Entity` 类中实现了 `__get` 和 `__set` 方法，据此来获取和设置对象属性。当然，这违反了封装性，是个折中方案，开发人员不应当过于依赖此
魔术方法来写业务逻辑代码。

[返回](../README.md)