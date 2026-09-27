# server：后端 API

Webman 后端位于本目录。项目依赖 PHP 8.2+；Composer 依赖和版本要求以 composer.json、composer.lock 为准。数据库、Redis 和监听地址按本地 .env 及 config 配置准备。

## 安装与运行

首次安装依赖：

    composer install

开发环境前台运行：

    php start.php start

生产环境后台运行：

    php start.php start -d

查看状态、停止或重启：

    php start.php status
    php start.php stop
    php start.php restart

Windows 下运行 .\windows.bat。该脚本检查 config/install.lock：存在时运行 windows.php，不存在时运行 install.php。

## 后端目录

- app/adminapi：后台接口，按 controller、logic、validate、lists 等目录组织。
- app/api：客户端接口。
- app/common：共享 model、service、enum、validate 等。
- config/route.php：显式路由配置。
- public：Webman 对外静态文件目录。

后台接口使用 /adminapi 前缀，客户端接口使用 /api 前缀。新增接口时检查路由、控制器、验证、业务逻辑、权限和调用端 API 定义。

## Docker Compose

Compose 配置位于本目录的 docker-compose.yaml。进入 server 后执行：

    docker compose up -d
    docker compose ps
    docker compose logs -f server
    docker compose down

运行前确认 Compose 使用的环境变量、端口和持久化配置符合当前环境。

## 部署路由

- /adminapi/ 和 /api/：反向代理到 Webman 服务。
- /resource/：按项目静态资源目录配置代理或静态文件服务。
- /admin：服务 server/public/admin 中的管理后台文件，并为前端路由配置 index.html 回退。
- /pc：服务 server/public/pc 中的 PC 文件，并为前端路由配置 index.html 回退。
- UniApp H5 构建产物位于 server/public/mobile，按实际访问路径配置静态服务。

admin、pc 和 UniApp H5 的发布脚本会先移除对应目标目录，再复制新文件。部署前确认目标目录中没有需要保留的文件。Nginx 的主机、端口、静态目录和缓存策略需按部署环境配置；不要直接使用示例占位值。
