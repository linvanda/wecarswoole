### Entity

创建类继承 `\WecarSwoole\Entity`：

```php
class Id extends \WecarSwoole\Entity
{
    protected $id;
    protected $type;

    public function __construct(string $id, string $type)
    {
        $this->id = $id;
        $this->type = $type;
    }
  	...
}

class User extends \WecarSwoole\Entity
{
    protected $name;
    protected $age;
    protected $sex;
    protected $id;

    public function __construct($name, $age, $sex, $idArr)
    {
        $this->name = $name;
        $this->age = $age;
        $this->sex = $sex;
        $this->id = new Id($idArr['id'], $idArr['type']);
    }
  	...
}

$user = new User("张三", 12, '男', ['id' => '2342112213', 'type' => '身份证']);
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