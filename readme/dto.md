### DTO(Data Transfer Object，数据传输对象)

DTO 并非严格意义上 OOD 中的对象，其起数据容器的作用，本质上属于数据结构范畴（《代码整洁之道》）。

DTO 在 OOD（特别是 DDD）中的一大作用是进行**用例层面**（和领域层面相对）的数据组装。

EasySwoole 中的 Bean 实际上就是 DTO（不过很多人把它用作和数据库打交道的 Model 或者 DAO 了）。

一个事实是，**用例(用户)层面的东西和领域层面不是一一对应的**，比如说用例（用户）层面需要同时看到订单以及其下面的商品信息，甚至还包括这些商品的热度等，这些在用例（用户）层面属于一条查询"任务"，但在领域层面，它们属于不同的业务领域（订单领域和商品领域），一般会由不同的系统提供。通常，我们会通过一个聚合服务从多个子系统获取到相关信息，然后将这些信息聚合在一起，然后组装成一个符合用例要求的 DTO 返回到控制器，控制器进一步解析成客户端需要的数据格式（如 json）返回。

另外，一种典型情况是查询任务（列表查询或单条查询），这种查询没有什么业务逻辑，仅仅是从数据库取数据然后组装下返回给客户端。这种我们就没有必要使用 Domain 中的 Service、Entity 等这些重概念，可以在 Controller 中直接调用 Repository 获取数据，Repository 返回的也不是 Entity 对象，而是针对用例优化结构的 DTO 对象（用例查询优化）。如果这种查询很多，建议将查询的方法抽离成单独的 Repository（CQRS，[命令查询职责分离模式](https://www.cnblogs.com/yangecnu/p/Introduction-CQRS.html)）

另一种情况是，客户端传过来的数据（入参，上面提到的是出参）很多，如果我们一个个传参到 Service 中，Service 的构造函数参数会很长，一般我们也可以创建一个 DTO 来传参。

为何我们使用 DTO 而不是数组传递参数？

目的在于可维护性。数组过于灵活，数组的内容是不受约束的，而且也无法从参数直接知道具体有哪些内容。使用DTO的好处是明确传递的数据内容。另外由于 DTO 不受领域概念的约束，可以向应用层和基础设施层作亲和，例如可以在 DTO 对象中定义跟数据库表字段的数据转换规则等（而领域层的 Entity 则不应该知道应用层和存储层的细节，不能在 Entity 中作数据字段映射）。

DTO 应当放在哪？

从上面的分析可知，DTO 不属于 Domain，属于用例维度的东西，具体实现上一般用在 Controller/handler 和 Repository 中，建议可以根据使用情况放置，例如仓储返回的直接放在 Foudation/Repository/目录下。或者干脆直接在 app/下的 DTO/ 目录下（默认没有这个目录）亦可。

### 创建 DTO：

继承 `\WecarSwoole\DTO`：

```php
class IdDTO extends \WecarSwoole\DTO
{
    protected $id;
    protected $type;
}

class UserDTO extends \WecarSwoole\DTO
{
    protected $name;
    protected $age;
    /**
     * @field gender
     * @mapping 1 => 女, 2 => 男
     */
    protected $sex;
    protected $liveAddress;
  	/**
  	 * 该属性是 DTO 子类，会被自动构建，详情参见 buildFromArray() 方法
  	 *@field identity
  	 *@var IdDTO
  	 */
    protected $id;
}

// 注意 $sex 属性的注解是如何映射到这里的 gender 字段的
$data = [
    'name' => '张三',
    'age' => 12,
    'gender' => 1,
    'live_address' => [
        'city' => '深圳',
        'area' => '福田'
    ],
    'identify' => [
        'id' => '13090909000032',
        'type' => '身份证',
    ]
];
$userDTO = new UserDTO($data);
var_export($userDTO->toArray());
```

toArray() 的默认返回格式(将驼峰转成下划线)：

```php
$userDTO->toArray();
// output:
array (
    'name' => '张三',
    'age' => 12,
    'sex' => '女',
    'id' => array (
        'id' => '13090909000032',
        'type' => '身份证',
    ),
    'live_address' => array (
        'city' => '深圳',
        'area' => '福田',
    ),
)
  
$userDTO->toArray(true, true, true);
// output(可将多维数组转成一维数组。此时应注意多维数组字段不要重名，否则会发生覆盖):
array (
    'name' => '张三',
    'age' => 12,
    'sex' => '女',
    'city' => '深圳',
    'area' => '福田',
    'id' => '13090909000032',
    'type' => '身份证',
)
```

说明：

1. 构造函数传入的数组会将下划线转成驼峰来匹配属性；
2. 可使用 @field 和 @mapping 注解分别做字段和值映射；
3. 在 toArray 过程中，如果属性是 DTO 类型，会递归解析；

### DTO 集合：

有两种方式创建 DTO：

`WecarSwoole\OTA\Collection` ：该集合接收 IExtractable 类型对象数组。

`WecarSwoole\OTA\DTOCollection`：该集合接收两个参数：DTO子类型以及用于创建 DTO 的二维数组。

两个类都提供了 toArray() 方法用于将 IExtractable 类型数组转为二维数组。


[返回](../README.md)