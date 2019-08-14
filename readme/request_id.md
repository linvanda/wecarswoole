### Request ID

**场景**

- 协程模式下，记录的日志并非按照请求顺序记录的，多个请求的日志可能穿插在一起，不好排查问题；
- 微服务模式下，用户一次请求可能会涉及到多个服务的级联调用，如 A -> B -> C -> D，排查问题时，希望知道本次请求的来源是哪里以及可追朔上游日志；
- 可以使用日志聚合服务将同一次请求的日志聚合到一起；

**解决方案**

框架基于行业通行做法，提供了 Request ID 功能。

每次请求到来，框架会为该次请求生成唯一的 request_id，该 request_id 是基于来源请求头 wcc-request-id 生成的新 id（请求头名称可以在配置文件中配置：request_id_key，如果没有来源头，则认为是初始请求）。

记录日志时，在日志消息中加入 request_id 信息。

使用 API::invoke(…) 调用其它服务时，会自动加上 wcc-request-id 头信息（请求头名称可配置。注意：该头部是在 `WecarSwoole\Client\Http\Component\WecarHttpRequestAssembler` 请求组装器中加的，要想加入此头部，必须使用该组装器或者其子类）。

**示例**

以下是一条日志信息：

YH.INFO: [OL-CP-YH-YH:abdfd-dkkekdf-9VB7J5Sy-yCuUq9Ld]come here

其中，[…] 里面的就是 request_id。分成两部分，用冒号(:)隔开：OL-CP-YH-YH 是服务调用链，说明本次请求的完整调用链是：油号 -> 券 -> 用户 -> 用户（用户服务调用了自己的另一个接口）；abdfd-dkkekdf-9VB7J5Sy-yCuUq9Ld 是随机串，其中 abdfd-dkkekdf-9VB7J5Sy 是从上游来的，yCuUq9Ld 是本次生成的。

据此 request_id 我们可以得知，本次调用链在油号系统的 request_id 是 OL:abdfd，在券系统的 request_id 是 OL-CP:abdfd-dkkekdf。（当然前提是其它系统也使用了此 request_id 机制）

