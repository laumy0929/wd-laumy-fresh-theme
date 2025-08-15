# Laumy Fresh Theme

一个简洁、现代、响应式 WordPress 主题。提供固定顶部导航、左侧基于菜单顺序的分类专栏、中间最新文章卡片、右侧个人资料+最近更新；文章页包含可折叠/跟随高亮的多级目录（H2–H5）、底部上一篇/下一篇导航与完整评论系统，并支持深色模式。站点实例：https://www.laumy.tech/

## 功能概览
- 主页三栏布局：左分类、中内容、右资料+最近更新
- 顶部导航：站点标题、搜索框、主题切换、首页与“支持与合作”链接
- 分类专栏：基于“外观→菜单”的顺序与层级（父/子两级），点击父级可展开子级；显示分类文章数量
- 最新文章：缩略图（优先级：特色图→正文首图→内置占位图）、标题、两行摘要、作者/分类/日期；分页每页 10 条
- 右侧栏：作者信息（自定义器字段：昵称、职业、描述、头像）与最近更新 10 篇
- 文章页：
  - 左侧为目录（H2–H5）支持折叠/展开、多级缩进、滚动跟随并高亮当前小节；目录项前自动生成层级编号（1 / 1.1 / 1.1.1…）
  - 中间正文自动注入标题锚点，图片/代码块/表格自适应宽度
  - 右侧同主页个人资料，但下方为“最新文章”
  - 底部“上一篇/下一篇”导航（同分类可按需扩展）
  - 评论区：与后台联动，支持楼中楼（嵌套回复）、Cookies 记忆（名称/邮箱），隐藏“网站”字段
- 深色模式：一键切换，持久化
- 性能：滚动高亮节流（requestAnimationFrame）、查询忽略置顶、no_found_rows 场景
- 响应式：宽屏充分利用空间；中小屏自动单列并保持可读性

## 目录结构
```
laumy-fresh-theme/
├─ assets/
│  └─ js/theme.js               # 深色模式、分类展开、目录折叠与滚动高亮
├─ header.php                   # 顶部导航
├─ footer.php                   # 页脚（版权与备案链接）
├─ index.php                    # 主页（分类专栏 + 最新文章 + 右侧栏）
├─ category.php                 # 分类页（与主页布局一致）
├─ search.php                   # 搜索结果页
├─ single.php                   # 文章内容页（目录/正文/右侧栏/上一篇下一篇/评论）
├─ comments.php                 # 评论模板（隐藏网站字段，姓名+邮箱同一行）
├─ functions.php                # 主题功能、数据与自定义项
├─ style.css                    # 主题样式与变量、响应式、深色模式
└─ README.md                    # 当前说明文档
```

## 安装与启用
1. 将 `laumy-fresh-theme` 拷贝到服务器目录：`wp-content/themes/`
2. 在 WordPress 后台 → 外观 → 主题 中启用 “Laumy Fresh Theme”。
3. 在 外观 → 自定义 → 作者信息 填写昵称、职业、描述与头像（URL）。
4. 在 外观 → 菜单 配置主菜单“Primary Menu”，添加分类（父/子两级）并按需排序；左侧分类栏将按该顺序与层级展示。

## 内容与显示
- 缩略图优先级：
  1) 特色图像（缩略图）
  2) 正文中的第一张图片
  3) 主题内置占位图（assets/images/default-profile.svg / default-thumbnail.svg）
- 摘要：后端输出完整纯文本，由 CSS `-webkit-line-clamp: 2` 截断为两行并溢出省略
- 分页：主页与分类页每页 10 篇，在底部 `paginate_links` 展示

## 文章目录（TOC）
- 抓取正文中的 H2–H5 生成树结构，输出嵌套列表
- 交互：
  - 点击可折叠/展开子目录
  - 页面滚动时自动高亮当前小节，节流以保证性能
- 样式：多级缩进，编号与标题紧凑对齐

## 评论系统
- 文章底部加载 `comments.php`
- 启用 HTML5 的 `comment-form`/`comment-list`
- 自动加载 WordPress `comment-reply` 脚本（后台启用“嵌套评论”即生效）
- 表单定制：去除“网站”字段；“显示名称/邮箱”同一行并带“必填/选填”占位提示；Cookie 提示仅包含名称和邮箱

## 自定义
- 自定义器（外观 → 自定义 → 作者信息）：
  - `profile_name`、`profile_job`、`profile_desc`、`profile_image`
- 颜色与间距：可在 `style.css` 顶部的 CSS 变量里调整：
  - `--color-bg`、`--color-text`、`--color-primary`、`--color-border`
  - 布局变量：`--header-height`、`--sidebar-left-width`、`--sidebar-right-width`、`--container-max-width`、`--gap`

## 开发说明
- 主要函数（`functions.php`）：
  - `laumy_fresh_get_thumbnail_url($post_id)`：计算文章缩略图 URL
  - `laumy_fresh_get_menu_categories()`：根据主菜单构建父/子分类树并保留顺序
  - `laumy_fresh_excerpt_full($content)`：生成供 CSS 截断的“完整摘要”
  - `laumy_fresh_category_count($term_id, $include_children)`：统计分类文章数（可含子级）
  - `laumy_fresh_get_site_stats()`：站点文章总数与阅读数（缓存）
  - `laumy_fresh_increment_post_views()`：文章阅读数累计（含简单反爬与 Cookie 防抖）
- 脚本（`assets/js/theme.js`）：
  - 深色模式切换并持久化
  - 分类父级展开/收起（事件代理）
  - TOC 折叠与滚动高亮（requestAnimationFrame 节流）

## 常见配置
- 调整“最近更新/最新文章”数量：在 `index.php`/`single.php` 中的 `WP_Query(['posts_per_page'=>10])`
- 只在同一分类内显示“上一篇/下一篇”：将 `previous_post_link()` / `next_post_link()` 第三个参数设为 `true`
- 评论必填项：后台“设置 → 讨论”中开启/关闭“评论作者必须填写姓名和电子邮件地址”

## 兼容性
- WordPress 5.0+（建议 6.x）
- PHP 7.4+
- 桌面与移动端现代浏览器

## 许可证
- GPL-2.0-or-later

## 变更日志
- v1.0.0: 首次发布
