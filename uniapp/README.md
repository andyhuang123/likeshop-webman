# uniapp：多端客户端

本目录是 UniApp 客户端，页面位于 src/pages，接口位于 src/api。页面与路由配置遵循 pages.json 和现有组件模式。

## 安装依赖

本目录提供 package-lock.json；按 npm 锁文件安装：

    npm ci

交互式开发和打包入口会检查 Node.js 16.16.0 及以上。

## 开发

    npm run dev
    npm run dev:h5
    npm run dev:mp-weixin

- dev 会交互选择微信小程序或 H5。
- dev:h5 和 dev:mp-weixin 直接启动对应平台。
- package.json 还定义了支付宝、百度、QQ、头条等平台脚本；使用前查看当前脚本清单和 UniApp 官方工具链要求。

## 构建

    npm run build
    npm run build:h5
    npm run build:mp-weixin

- build 会交互选择微信小程序或 H5。
- build:h5 会运行 UniApp H5 构建，并运行 scripts/release.mjs。
- release.mjs 会移除 server/public/mobile，再复制 H5 产物；确认目录可被替换后再执行。
- build:mp-weixin 直接运行微信小程序构建脚本。

完整命令以 package.json 为准。
