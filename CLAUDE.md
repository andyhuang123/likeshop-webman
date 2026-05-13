# CLAUDE.md - likeadmin-webman 项目配置指南

## 项目概述

**likeadmin-webman** 是一个基于 **Webman** (PHP高性能HTTP服务框架) 的多端企业级后台管理系统。项目采用前后端分离架构，支持管理后台、PC端和UniApp小程序多端应用。

- **后端**: PHP 8.2+、Webman、ThinkORM、MySQL、Redis
- **管理后台**: Vue 3、TypeScript、Vite、Element Plus、Pinia
- **PC端**: Nuxt 3、Vue 3、TypeScript、Element Plus
- **小程序端**: UniApp、Vue 3、TypeScript

## 目录结构

```
likeadmin-webman/
├── server/          # 后端API服务
│   ├── app/        # 应用代码
│   │   ├── adminapi/      # 后台API模块
│   │   │   ├── controller/    # 控制器层
│   │   │   ├── logic/         # 业务逻辑层
│   │   │   ├── service/       # 服务层
│   │   │   ├── validate/      # 数据验证层
│   │   │   └── lists/         # 列表数据层
│   │   ├── api/           # 前台API模块
│   │   ├── common/        # 公共模块
│   │   │   ├── model/     # 数据模型（统一管理）
│   │   │   ├── service/   # 公共服务
│   │   │   └── enum/      # 枚举定义
│   │   └── queue/         # 消息队列
│   ├── config/        # 配置文件
│   │   ├── thinkorm.php    # 数据库配置
│   │   ├── route.php       # 路由配置
│   │   └── project.php     # 项目配置
│   └── public/        # 静态资源
├── admin/             # 管理后台前端
│   ├── src/
│   │   ├── api/      # API接口定义
│   │   ├── views/    # 页面组件
│   │   ├── components/ # 公共组件
│   │   ├── router/   # 路由配置
│   │   ├── stores/   # 状态管理
│   │   └── utils/    # 工具函数
├── pc/                # PC端前端 (Nuxt 3)
├── uniapp/            # 小程序端 (UniApp)
└── CLAUDE.md          # 本文件
```

## 技术栈详情

### 后端 (server/)
- **框架**: Webman (基于Workerman)
- **PHP版本**: >= 8.2
- **ORM**: ThinkORM (ThinkPHP的ORM组件)
- **数据库**: MySQL
- **缓存/队列**: Redis
- **依赖管理**: Composer
- **文件存储**: 阿里云OSS、七牛云、腾讯云COS
- **支付集成**: 微信支付、支付宝
- **微信生态**: EasyWeChat SDK

### 管理后台 (admin/)
- **框架**: Vue 3 + TypeScript
- **构建工具**: Vite
- **UI框架**: Element Plus
- **状态管理**: Pinia
- **路由**: Vue Router 4
- **样式**: Tailwind CSS + SCSS
- **HTTP客户端**: Axios
- **代码质量**: ESLint + Prettier
- **打包工具**: Node.js + npm/yarn/pnpm

### PC端 (pc/)
- **框架**: Nuxt 3 (SSR/SSG)
- **UI框架**: Element Plus
- **样式**: Tailwind CSS
- **状态管理**: Pinia
- **路由**: Nuxt文件式路由

### 小程序端 (uniapp/)
- **框架**: UniApp (Vue 3)
- **跨平台**: 支持微信、支付宝、百度等多端小程序
- **路由**: uniapp-router-next
- **样式**: Tailwind CSS (通过weapp-tailwindcss-webpack-plugin适配)

## 开发工作流

### 后端开发规范

#### 1. 代码分层架构
```
Controller (控制器) → Logic (业务逻辑) → Service (服务层) → Model (模型层)
```

#### 2. 目录规范
- **控制器**: `server/app/adminapi/controller/{模块名}/`
- **业务逻辑**: `server/app/adminapi/logic/{模块名}/`
- **数据模型**: `server/app/common/model/{模块名}/` (统一管理)
- **数据验证**: `server/app/adminapi/validate/{模块名}/`
- **列表数据**: `server/app/adminapi/lists/{模块名}/` (分页查询逻辑)

#### 3. 命名约定
- 控制器类名: `XxxController` (如 `UserController`)
- 方法名: 使用小驼峰，如 `getList`, `addUser`
- 数据库表名: 小写+下划线，如 `user_info`
- 模型类名: 大驼峰，如 `UserInfo`

#### 4. 路由配置
- 后台API路由前缀: `/adminapi/{模块名}`
- 前台API路由前缀: `/api/{模块名}`
- 路由定义在 `server/config/route.php`

### 前端开发规范

#### 1. API管理
- API接口定义在 `admin/src/api/` 目录下按模块组织
- 使用TypeScript定义接口类型
- API调用使用统一的请求拦截器和响应处理器

#### 2. 组件规范
- 页面组件: `admin/src/views/{模块名}/`
- 公共组件: `admin/src/components/`
- 组件命名: 大驼峰，如 `UserList.vue`
- 组件结构: `<template> <script setup lang="ts"> <style scoped>`

#### 3. 状态管理
- 使用Pinia进行状态管理
- Store模块化组织在 `admin/src/stores/modules/`
- 避免在组件中直接修改store状态

#### 4. 路由管理
- 路由配置在 `admin/src/router/routes.ts`
- 动态路由根据权限加载
- 路由守卫处理权限验证

## 常用命令

### 后端开发
```bash
# 进入server目录
cd server

# 安装依赖
composer install

# 开发环境启动
php start.php start

# 生产环境启动
php start.php start -d

# Windows环境启动
./windows.bat

# 查看运行状态
php start.php status

# 停止服务
php start.php stop

# 重启服务
php start.php restart

# 代码格式化 (如果配置了)
composer format
```

### 管理后台开发
```bash
# 进入admin目录
cd admin

# 安装依赖 (根据项目使用的包管理器)
npm install      # 或 yarn install 或 pnpm install

# 开发环境启动
npm run dev      # 或 yarn dev 或 pnpm dev

# 构建生产版本
npm run build    # 或 yarn build 或 pnpm build

# 预览构建结果
npm run preview  # 或 yarn preview 或 pnpm preview

# 代码检查
npm run lint     # 或 yarn lint 或 pnpm lint

# 类型检查
npm run type-check  # 或 yarn type-check 或 pnpm type-check
```

### PC端开发
```bash
# 进入pc目录
cd pc

# 安装依赖
npm install

# 开发环境启动
npm run dev

# 构建静态站点
npm run build

# 构建SSR应用
npm run build:ssr

# 预览
npm run preview
```

### 小程序端开发
```bash
# 进入uniapp目录
cd uniapp

# 安装依赖
npm install

# 微信小程序开发
npm run dev:mp-weixin

# H5开发
npm run dev:h5

# 构建
npm run build
```

## 部署指南

### Docker部署
```bash
# 在项目根目录
docker-compose up -d

# 查看服务状态
docker-compose ps

# 查看日志
docker-compose logs -f server

# 停止服务
docker-compose down

# 重启服务
docker-compose restart
```

### Nginx配置要点
1. 后端API代理到 `http://ip:端口/adminapi/` 和 `/api/`
2. 静态资源代理到 `/resource/` 并配置缓存
3. 前端页面配置伪静态路由
4. 详细配置参考项目根目录的README.md

## 代码生成器

项目集成了代码生成器功能，可以快速生成：
- 数据库模型类
- CRUD控制器
- 业务逻辑层
- 数据验证器
- 前端页面组件

## 注意事项

### Git工作流
- 当前分支: `develop`
- 主分支: `develop`
- 提交前确保代码格式化和类型检查通过

### 环境配置
- 后端环境变量配置在 `server/.env`
- 前端环境变量配置在各端目录的 `.env.*` 文件中
- 数据库配置从环境变量读取

### 安全规范
1. 所有用户输入必须经过验证
2. SQL查询使用参数绑定防止注入
3. 文件上传限制文件类型和大小
4. 敏感信息不写入日志
5. API接口需要适当的权限验证

### 性能优化
1. 数据库查询使用索引优化
2. 频繁访问的数据使用Redis缓存
3. 大文件上传使用分片上传
4. 前端资源按需加载

## 故障排除

### 常见问题
1. **端口占用**: 修改 `server/config/server.php` 中的端口配置
2. **数据库连接失败**: 检查 `server/.env` 中的数据库配置
3. **前端构建失败**: 检查Node.js版本和依赖安装
4. **权限问题**: 确保运行时目录有正确的写入权限

### 调试工具
- 后端日志: `server/runtime/logs/`
- 前端开发工具: Vue DevTools
- 网络请求: 浏览器开发者工具

## 扩展开发

### 添加新模块步骤
1. 数据库创建对应表结构
2. 后端: 创建模型、控制器、逻辑层、验证器等
3. 前端: 创建API接口、页面组件、路由配置
4. 配置权限菜单
5. 测试完整功能链路

### 插件开发
- Webman插件系统支持扩展功能
- 插件目录: `server/config/plugin/`
- 可扩展中间件、进程、命令等

---

*本文件最后更新: 2026-04-07*  
*项目版本: 1.9.4 (参考 server/config/project.php)*