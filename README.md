# Life 博客系统（开源版）

一款可自托管的 PHP/MySQL 个人博客系统，首次访问自动进入安装向导，无需手工建表。基于 yunxi0723/yxos 二次开发，沿用 GPL-3.0 许可证，保留上游版权。

- **作者 QQ**：78013314
- **协议**：GPL-3.0
- **环境**：PHP 8.1+ / MySQL 8

## 功能特性

- 博客文章：发布、编辑、归档、前台评论（支持审核开关）
- 朋友圈 / 说说：时间线式动态发布
- 相册画廊：3:4 自动比例缩略图，点击查看原图
- 友链页面、关于页面、留言板
- 后台管理：站点设置、用户管理、权限分级、评论审核
- 上传系统：本地存储，支持头像、文章封面、画廊图片
- 音乐播放器：内置播放列表
- 安装向导：浏览器一键安装，自动建表并生成配置
- 安全防护：登录限流、CSRF 校验、目录隔离、远程文件加锁

## 环境要求

- PHP 8.1 或更高版本
- MySQL 8.0+
- PHP 扩展：mysqli、curl、fileinfo、exif，建议开启 GD
- Web 服务器：Apache（需 AllowOverride）或 Nginx

## 部署教程

### 1. 上传代码

解压本仓库全部文件到网站根目录，**必须包含隐藏文件 `.htaccess`**。

### 2. 准备数据库

新建一个空的 UTF-8 MySQL 数据库，并创建拥有建表权限的账号。
> 安装程序会拒绝已有数据表的数据库，不会覆盖旧数据；若安装中途失败，请换新的空数据库重试。

### 3. 设置目录权限

确保 PHP 进程对以下目录有写入权限：

```
upload/
user/
user/headimg/
user/life/
user/site-images/
```

### 4. 运行安装向导

浏览器访问：

```
https://你的域名/install/
```

按页面提示填写：

- 数据库地址（`localhost` 或 `主机名:端口`）
- 数据库名、数据库账号、数据库密码
- 自定义管理员账号和密码

安装程序会自动创建全部数据表、初始化导航、生成 `config.php` 并写入安装锁。

### 5. 安装后收尾

1. 登录后台 `/admin/`，设置站点名称、Logo、背景图等资料。
2. **删除 `install/` 目录**。
3. 收回网站根目录写权限，仅保留 `upload/`、`user/` 可写。
4. 强制启用 HTTPS。

## Web 服务器配置

### Apache

需在站点配置中启用 `AllowOverride All`，使随附的 `.htaccess` 生效。

### Nginx

需要额外配置以下规则：

- 禁止访问 `/includes/`、`/migrations/` 目录
- 禁止访问隐藏文件和 `.sql` 文件
- 在 `/upload/`、`/user/` 目录下禁止执行 PHP
- 安装期间允许访问 `/install/`，完成后删除该目录

本项目无需伪静态即可正常使用。

## 目录结构

```
├── index.php          # 前台首页
├── home.php           # 朋友圈
├── view.php           # 文章详情
├── archives.php       # 归档
├── gallery.php        # 相册
├── links.php          # 友链
├── about.php          # 关于
├── edit.php           # 发布/编辑
├── admin/             # 后台管理
├── api/               # 接口
├── includes/          # 核心类库
├── install/           # 安装向导（用完即删）
├── migrations/        # 数据库迁移
├── assets/            # 静态资源
├── upload/            # 上传目录
└── user/              # 用户数据目录
```

## 安全说明

- 安装后生成的 `config.php` 包含数据库密码，**请勿提交到公开仓库**。
- 本仓库已通过 `.gitignore` 排除 `config.php` 及运行时数据。
- 正式部署前请在空数据库上完整试装一次。

## 版权

基于 [yunxi0723/yxos](https://github.com/yunxi0723/yxos) 二次开发，采用 **GNU General Public License v3.0** 开源协议。商业使用请遵守 GPL-3.0 条款。
