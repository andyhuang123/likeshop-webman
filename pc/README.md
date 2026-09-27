# pc：Nuxt PC 端

本目录是 Nuxt 3 PC 应用，页面位于 pages，接口与共享逻辑沿用 api、composables 等目录。

## 依赖管理

当前目录包含 package-lock.json 和 yarn.lock，package.json 未声明 packageManager。仓库没有指定唯一包管理器；安装或更新依赖前按团队约定选择一种，并只维护对应锁文件。以下以 npm 运行脚本为例。

## 开发和构建

    npm run dev
    npm run build
    npm run build:ssr
    npm run preview
    npm run start

- build 执行 nuxt generate，生成静态站点，然后运行 scripts/build.mjs。
- build:ssr 执行 nuxt build，然后运行 scripts/build.mjs；发布内容受 NUXT_SSR 环境变量影响。
- scripts/build.mjs 会移除 server/public/pc，再复制新的构建目录。确认该目录可被替换后再构建发布版本。
- start 运行 nuxt start，适用于 Nuxt 构建产物；部署方式应与当前 NUXT_SSR 配置保持一致。

查看 package.json 和 nuxt.config.ts 以确认脚本及运行配置。
