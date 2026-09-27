# admin：管理后台

本目录是 Vue 3、TypeScript、Vite 管理后台。接口定义位于 src/api，页面位于 src/views，共用组件位于 src/components。

## 依赖管理

当前目录同时包含 package-lock.json、pnpm-lock.yaml 和 yarn.lock，package.json 未声明 packageManager。仓库没有指定唯一包管理器；安装或更新依赖前按团队约定选择一种，并只维护对应锁文件。以下以 npm 运行脚本为例。

## 开发与检查

    npm run dev
    npm run type-check
    npm run lint
    npm run build
    npm run preview

- type-check 使用 vue-tsc 检查类型。
- lint 使用 ESLint 的 --fix 参数，会自动修改可修复的问题；执行前先检查工作区。
- build 会先构建 Vite 产物，再运行 scripts/release.mjs。
- release.mjs 会移除 server/public/admin 后，将 dist 复制到该目录；确认目标目录可被替换后再构建发布版本。

查看 package.json 以确认脚本的当前定义。
