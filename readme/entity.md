### Entity

Entity 一般和数据库记录对应（但不代表说必须要和数据表一一对应），具有唯一标识，是领域对象的核心。两个 Entity 相等的充要条件是两者的 id 相等。

Entity 属于领域构建中的小粒度单元，不应当和其他对象存在过于复杂的依赖关系——如果出现过于复杂的依赖关系，说明 Entity 需要拆分，或者某些逻辑实现需要提取成 Service。

Entity 不应当对基础设施有任何依赖——框架属于典型的基础设施。对基础设施的任何依赖都应当转变成对接口的依赖，并通过依赖注入提供依赖的具体实现。换句话说，一个 Entity 类可以不加修改地迁移到其他地方（如其他框架中）。

> 为何在基类 `\WecarSwoole\Entity` 中提供了 getter、setter？
> 从设计原则来说，这是反模式的，破坏了面向对象的封装特性。
> 这么做的原因是，我们需要在仓储（Repository）中获取和设置对象的所有属性（以存储到数据库或者从数据库获取出来），而一般对象的属性都是非 public 的，因而正常是无法访问的，可能要通过各种复杂技巧才能去访问和设置。因而，为了简化使用，我们在基类里面提供了 getter、setter 这种反模式。实际开发中需要遵循约定：业务代码中不要直接访问对象的属性，特别是受保护的属性，而应该通过调接口去操作。

继承 `\WecarSwoole\Entity`：

```php
<?php

namespace App\Domain\User;

use App\DTO\User\UserDTO;
...

class User extends Entity
{
    public const GENDER_MALE = 1;
    public const GENDER_FEMAIL = 2;
    public const GENDER_UNKNOW = 0;

    public const UPDATE_NONE = 1;
    public const UPDATE_ONLY_NULL = 2;
    public const UPDATE_NEW = 3;

    /** @var UserId $userId 是内部标识，不应当对外暴露*/
    protected $userId;
    protected $name;
    protected $nickname;
    protected $gender;
    protected $birthday;
    /** @var string */
    protected $regtime;
    protected $headurl;
    protected $tinyHeadurl;
    /**
     * 用户来源
     * @var string
     */
    protected $registerFrom;
    /**
     * 车牌号列表
     * @var array
     */
    protected $carNumbers = [];
    /**
     * 生日修改次数
     * @var int
     */
    protected $birthdayChange = 0;
    /**
     * 邀请码
     * @var string
     */
    protected $inviteCode;

    public function __construct(UserDTO $userDTO = null)
    {
        if ($userDTO) {
            // 从 DTO 创建 User 对象
            $this->buildFromArray($userDTO->toArray());
        }

        // 组装 user 标识
        $this->userId = new UserId($userDTO->uid, $userDTO->phone, $userDTO->relUids ?? [], $userDTO->partners);

        // 邀请码
        if (!$this->inviteCode) {
            $this->inviteCode = Random::str(12);
        }

        $this->regtime = $this->regtime ?: date('Y-m-d H:i:s');
    }

    public function uid(): int
    {
        return $this->userId->getUid();
    }

    public function setUid(int $uid)
    {
        $this->userId->setUid($uid);
    }

    public function partners(): PartnerMap
    {
        return $this->userId->getPartners();
    }

    public function addPartner(Partner $partner)
    {
        $this->userId->addPartner($partner);
    }

    /**
     * @param int $type
     * @param $flag
     * @return Partner|null
     * @throws \WecarSwoole\Exceptions\InvalidOperationException
     */
    public function getPartner(int $type, $flag = null): ?Partner
    {
        return $this->userId->getPartner($type, $flag);
    }

    public function relUids(): array
    {
        return $this->userId->getRelUids();
    }

    public function phone()
    {
        return $this->userId->getPhone();
    }

    public function equal(User $user): bool
    {
        return $user && $user->userId->getUid() === $this->userId->getUid();
    }

    /**
     * 基于 DTO 信息更新自身信息
     * @param UserDTO $userDTO
     * @param IUserRepository $userRepository
     * @param int $updateStrategy 更新策略
     * @param bool $forceChangePhone 是否强制修改手机号，如果需要修改手机号，必须设置为 true
     * @throws BirthdayException
     * @throws Exception
     * @throws InvalidPhoneException
     * @throws PartnerException
     */
    public function updateFromDTO(
        UserDTO $userDTO,
        IUserRepository $userRepository,
        int $updateStrategy = self::UPDATE_ONLY_NULL,
        $forceChangePhone = false
    ) {
        if ($updateStrategy === self::UPDATE_NONE) {
            return;
        }

        $this->validateDataFormat($userDTO);

        if ($updateStrategy === self::UPDATE_ONLY_NULL) {
            $this->updateIfNull($userDTO);
        } elseif ($updateStrategy === self::UPDATE_NEW) {
            $this->updateToNew($userDTO, $userRepository, $forceChangePhone);
        }
    }

  	...
}
```

同 DTO，Entity 也可以调用 toArray() 将对象转成数组，方便对外输出：

```php
$user->toArray();
// output:
array (
    'name' => '张三',
    'age' => 12,
    'sex' => '男',
    'id' => array (
        'id' => '2342112213',
        'type' => '身份证',
    ),
);

$user->toArray(true, true, true);
// output:
array (
    'name' => '张三',
    'age' => 12,
    'sex' => '男',
    'id' => '2342112213',
    'type' => '身份证',
)
```

Entity 可以调用 buildFromArray($array) 从数组构建对象。

> 注：虽然 Entity 和 DTO 一样实现了 `IExtractable` 和 `IArrayBuildable` 接口，可以基于数组构建对象，以及将对象转成数组，不过在实践中要慎用此功能，表面上便捷的功能却造成了系统设计上的不稳定性，它们会造成跨层对象/数据结构之间的耦合。软件设计没有银弹。

[返回](../README.md)