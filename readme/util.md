### 框架提供的 Util 工具

除了 easyswoole 提供的一些工具以外，框架还提供了以下工具供使用（在 \WecarSwoole\Util 下）：

- `AnnotationAnalyser`：注解分析器。
  - `getPropertyAnnotations(string $className, array $annotationFilters = []):array`：获取类属性注解信息
- `File`：文件/目录操作工具，继承 `\EasySwoole\Utility\File`。
  - `join(...$paths):string`：拼接文件名
  - … easyswoole 提供的功能
- `Url`：Url 辅助类。
  - `realUrl(string $path, array $queryParams = [], array $flagParams = [])`：根据配置文件生成绝对 url。
  - `assemble(string $uri, string $base = '', array $queryParams = [], array $flagParams = []): string`：组装 url
  - `parse(string $url): array`：解析出 schema,host,path,query_string
- `Mock`：模拟数据生成器。
- `Concurrent`：并发执行业务逻辑，并等待所有逻辑执行完成后返回所有的执行结果。注意必须在协程上下文中使用。使用实例：
  ```
    // 便捷使用
    $a = $b = $c = 5;
    echo "start:" . time()."\n";
    $r = Concurrent::simpleExec(
        function() use ($a, $b, $c) {
            Co::sleep(1);
            return "$a - $b - $c";
        },
        function () {
            Co::sleep(2);
            return "------";
        },
        function () {
            Co::sleep(1);
            throw new \Exception("我错了", 300);
        }
    );
    echo "end:" . time() . "\n";

    foreach ($r as $rt) {
        if ($rt instanceof \Throwable) {
            echo $rt->getMessage();
        } else {
            echo $rt;
        }
        echo "\n";
    }

    // 更复杂的使用（带传参）
    $r = Concurrent::instance()
    ->addParams([1, 3, 5], [], ['a', 'b', 'c'])
    ->addTasks(
        function($a, $b, $c) {
            Co::sleep(1);
            return "$a - $b - $c";
        },
        function () {
            Co::sleep(2);
            return "------";
        },
        function ($a, $b, $c) {
            Co::sleep(1);
            throw new \Exception("我错了", 300);
        }
    )->exec();
    ...
  ```


[返回](../README.md)

