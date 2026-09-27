# likeadmin-webman

基于 Webman 的 PHP 后端与 Vue、Nuxt、UniApp 多端应用仓库。

## 项目组成

- server：Webman API 服务，后台接口前缀为 /adminapi，客户端接口前缀为 /api。
- admin：Vue 3 + TypeScript + Vite 管理后台。
- pc：Nuxt 3 PC 端。
- uniapp：UniApp 客户端。

开发规范见 AGENTS.md。模块专属命令和构建行为见各模块 README。

## 环境要求

- 后端：PHP 8.2+、Composer；数据库和 Redis 按 server 的本地配置提供。
- 前端：Node.js 与模块所需的包管理器。uniapp 的交互式开发和发布入口要求 Node.js 16.16.0+。
- server 目录包含 Docker Compose 配置；部署说明见 server/README.md。

admin 和 pc 目录各有多个 JavaScript 锁文件，仓库没有声明唯一包管理器。安装或更新依赖前，按团队约定选定包管理器并只维护对应锁文件。以下命令以 npm 运行已定义脚本为例。

## 本地开发

后端（从仓库根目录执行）：

    cd server
    composer install
    php start.php start

Windows 可在 server 目录运行 .\windows.bat；脚本会在安装状态文件存在时调用 windows.php，否则运行 install.php。

管理后台：

    cd admin
    npm run dev
    npm run type-check
    npm run build

npm run lint 会调用 ESLint 自动修复文件，运行前先检查工作区改动。

PC 端：

    cd pc
    npm run dev
    npm run build

npm run build 生成静态版本；npm run build:ssr 用于 Nuxt SSR 构建。

UniApp：

    cd uniapp
    npm run dev
    npm run dev:h5
    npm run dev:mp-weixin

npm run dev 是交互式入口，可选择微信小程序或 H5。其他目标平台的脚本见 uniapp/package.json。

## 构建产物

部分构建脚本会直接替换 server/public 下的发布目录：

- admin 的 build 会替换 server/public/admin。
- pc 的 build 脚本会替换 server/public/pc。
- uniapp 的 H5 构建会替换 server/public/mobile。

运行这些构建前，请确认对应发布目录中的内容可以被替换。更多细节见各模块 README。

## 部署

Docker Compose 文件位于 server/docker-compose.yaml；从 server 目录执行 Compose 命令。Nginx 应将 /adminapi、/api 和 /resource 转发到后端，并将管理后台、PC 端及 H5 静态文件映射到相应发布目录。具体配置需按实际域名和目录调整，见 server/README.md。

## 开发规范

- [仓库开发规范](AGENTS.md)
- [后端说明](server/README.md)
- [管理后台说明](admin/README.md)
- [PC 端说明](pc/README.md)
- [UniApp 说明](uniapp/README.md)
