### 框架提供的 Util 工具

除了 easyswoole 提供的一些工具以外，框架还提供了以下工具供使用：

- `\WecarSwoole\Util\AnnotationAnalyser`：注解分析器
  - `getPropertyAnnotations(string $className, array $annotationFilters = []):array`：获取类属性注解信息
- `\WecarSwoole\Util\File`：文件/目录操作工具，继承 `\EasySwoole\Utility\File`
  - `join(...$paths):string`：拼接文件名
  - … easyswoole 提供的功能
- `\WecarSwoole\Util\Url`：Url 辅助类
  - `assemble(string $uri, string $base = '', array $queryParams = [], array $flagParams = []): string`：组装 url
  - `parse(string $url): array`：解析出 schema,host,path,query_string
