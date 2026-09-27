# likeadmin-webman 开发规范

本文件是仓库级开发规范的唯一来源。开始改动前，先阅读本文件和目标模块 README；涉及业务流程时，从实际路由、调用方和数据路径核对后再改。

## CodeGraph

当仓库根目录存在 .codegraph/ 时，在使用 rg、文件搜索或阅读源码来定位代码前，先运行 codegraph explore 查询相关符号和调用路径。若 .codegraph/ 不存在，则跳过 CodeGraph。CodeGraph 用于定位；涉及当前命令、配置或行为的结论，仍需对照当前工作区文件核实。

## 工作原则

- 先明确目标、成功标准和假设。存在歧义或相互矛盾的要求时，列出解释并询问，不默默猜测。
- 采用满足目标的最小实现；不为一次性需求增加抽象，不做无关重构。
- 只修改任务相关内容并匹配现有风格；因修改造成的死代码应清理，原有无关死代码只记录不顺手删除。
- 按改动范围选择验证方式。修复问题时优先覆盖复现路径；报告时说明验证实际覆盖的边界。

## 项目结构

- server：PHP 8.2+、Webman 2.1、ThinkORM 的后端服务。后台接口位于 app/adminapi，前台接口位于 app/api，共享代码位于 app/common。
- admin：Vue 3、TypeScript、Vite 管理后台。
- pc：Nuxt 3 PC 端。
- uniapp：UniApp 多端客户端。
- 各模块独立管理依赖和配置；根目录没有统一的 JavaScript package.json。

## 后端开发

后台模块主要目录：

- server/app/adminapi/controller：HTTP 控制器和接口入口。
- server/app/adminapi/logic：业务逻辑。
- server/app/adminapi/validate：请求校验。
- server/app/adminapi/lists：列表查询和分页。
- server/app/common/model、service、enum、validate：跨模块共用的数据和服务。
- server/config/route.php：显式路由配置。

新增或修改接口时，沿用相邻模块的实际结构：控制器处理请求、校验并组织响应；业务规则放在 Logic；列表查询复用 Lists；跨模块能力放入 common。不要因为目录名称而假定某层必须新增文件，先确认它在相邻代码中的用法。

后台 API 使用 /adminapi 前缀，客户端 API 使用 /api 前缀。新增路由时同步检查调用端 API 定义、权限配置和对应验证逻辑。

## 前端开发

- admin：API 定义放在 admin/src/api，页面位于 admin/src/views，共用组件位于 admin/src/components；路由配置在 admin/src/router。
- pc：遵循 Nuxt 目录约定，页面位于 pc/pages，接口与共享逻辑分别沿用 pc/api、pc/composables 等现有目录。
- uniapp：页面位于 uniapp/src/pages，接口位于 uniapp/src/api；路由和页面配置遵循 uniapp/src/pages.json 及现有组件模式。

新增页面或接口前，先检查目标模块中相邻功能的命名、请求封装、错误处理和权限方式；保持一致，不引入一次性抽象。

## 依赖、配置与安全

- server 的 PHP 最低版本由 server/composer.json 声明为 8.2。
- admin 和 pc 同时存在多个 JavaScript 锁文件，package.json 没有声明 packageManager。仓库目前未统一指定包管理器；依赖操作前按团队约定选定一种，只更新对应锁文件，不顺手删除其他锁文件。
- uniapp 的交互式 dev/build 脚本会检查 Node.js 16.16.0 及以上；其他前端模块未在 package.json 中声明统一 Node.js 版本。
- 使用各模块的本地环境配置，不提交真实密钥、令牌或生产配置。日志、示例和测试数据中也不要包含真实秘密。
- 外部输入经过对应 Validate；SQL 使用参数绑定；上传校验类型和大小；接口保留适当的登录和权限检查。
- 查询或修改数据结构前核对当前模型、迁移和数据库字段；不要凭文档或旧代码猜字段。

## 修改与检查

1. 先确认当前工作区、实际入口、调用链和相关配置，保留与任务无关的改动。
2. 只改达成目标所需的文件；沿用附近代码风格，不做无关重构。
3. 检查目标模块 package.json 或 composer.json 中实际存在的脚本，再选择适用检查；不要假设仓库提供未声明的 test、lint 或格式化命令。
4. 报告检查结果时，区分源码或静态检查与数据库、HTTP、支付、并发等运行环境验证。
5. 文档中的目录、版本、命令和构建产物路径必须能在当前仓库中核对；脚本改变时同步维护相关 README。
6. 按任务指定的分支和提交范围工作，不在规范中固定写某个当前分支；提交时只包含任务相关文件。

## 本地命令

各模块的详细命令和构建行为见对应 README：

- 后端：server/README.md
- 管理后台：admin/README.md
- PC 端：pc/README.md
- UniApp：uniapp/README.md
- 项目入口：README.md
