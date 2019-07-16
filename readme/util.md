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
- `RSA`：非对称加密（从微信端迁移过来）。


[返回](../README.md)

