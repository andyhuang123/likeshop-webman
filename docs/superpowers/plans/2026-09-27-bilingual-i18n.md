# 中英多语言支持实施计划

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** 为 admin、PC、UniApp 和后端提示实现 `zh-CN` / `en-US` 切换，默认中文并回退到中文。

**Architecture:** 前端各自加载本地 `vue-i18n` 资源并持久化语言选择；每个 API 请求发送 `Accept-Language`。后端以请求级 locale 翻译验证和响应消息，保留原 JSON 结构和业务码，不保存跨请求的进程级语言状态。

**Tech Stack:** PHP 8.2+、Webman 2.1、Vue 3、TypeScript、Vite、Nuxt 3、UniApp、`vue-i18n` 9.1.9（与 UniApp 当前版本一致）。

**Spec:** [2026-09-27-i18n-design.md](../specs/2026-09-27-i18n-design.md)

## 实施状态（2026-09-28）

- Admin、PC、UniApp 页面词条和后端本地化已实现；Task 1–8 的代码提交均已完成。
- 通过：PHP locale 检查与语法检查、Admin Vite build、PC Nuxt generate/build、UniApp H5 和微信小程序 build、`git diff --check`。
- 未完成的验收项：Admin type-check 仍报仓库既有 JSX/router/form/widget 类型错误；管理后台、PC 与 UniApp 原生端的语言切换和刷新持久化未完成手动验收。
- API 请求级 HTTP 验收受本地 Redis AUTH 配置阻断：请求在 `LoginMiddleware` 访问缓存时失败，未到达校验层。同一 worker 的英中连续请求因此未验证；CORS preflight 已确认接受 `Accept-Language`。
- PC 静态生成页已显示中文语言入口和账号页。切换菜单的自动化点击未能展开；构建结果不代表运行时切换验收。
- 提交：`a4c376e`（admin UI）、`4882d50`（PC locale）、`5763752`（PC UI）、`5349558`（UniApp locale）、`e30142b`（UniApp UI）、`986810f`（integration）。

## Global Constraints

- 对外 locale 固定为 `zh-CN`、`en-US`；默认和 fallback 均为 `zh-CN`。
- Webman locale 状态必须限定在单个请求内；同一 worker 的后续请求不得继承前一请求语言。
- API 的 `code`、`show`、`data`、HTTP 状态及响应结构保持不变，只允许本地化 `msg`。
- 首期不改商品、文章、站点配置等业务内容，不新增数据库表或迁移。
- PHP 最低版本为 8.2；UniApp 交互式开发和发布入口要求 Node.js 16.16.0+。
- admin 和 PC 未声明统一包管理器；依赖变更前使用团队选定的包管理器，并只更新其对应锁文件。
- 不运行带 `--fix` 的 lint 脚本；admin、PC、UniApp 发布构建会替换 `server/public` 下的发布目录，验证时使用底层本地构建命令，不运行 release 脚本。

## Review Focus

- `Accept-Language` 含多个语言和质量值时只选支持语言；不支持值回退中文。用 resolver 测试覆盖 `en-US,en;q=0.9`、`zh-CN,zh;q=0.8`、`fr-FR` 和空值。
- API 错误翻译缺失或占位符参数缺失时不返回空串或翻译键。用消息目录测试覆盖命中、回退、占位符替换及缺少替换值。
- 同一 Webman worker 连续处理英文、中文请求时语言不串用。用本地 HTTP 顺序请求验收。
- PC SSR 与静态构建读取持久化语言时不得产生水合错误。分别构建 SSR 和静态版本，并检查首屏初始化。
- UniApp H5 与微信小程序的语言持久化和组件默认文案一致。分别在两种目标上构建并手动切换验收。

---

## File Map

- `server/app/common/service/LocaleService.php`：locale 白名单、header 解析、翻译键和旧消息别名解析。
- `server/app/common/http/middleware/LocaleMiddleware.php`：在请求入口建立和清理请求级 locale 上下文。
- `server/config/middleware.php`：让 `/api` 与 `/adminapi` 共用语言处理中间件。
- `server/resource/translations/{zh_CN,en}/messages.php` 和 `server/resource/translations/en/validate.php`：业务提示与验证规则译文。
- `server/app/common/service/JsonService.php`、`server/app/common/exception/Handler.php`：在现有统一响应和异常出口本地化 `msg`。
- `server/tests/locale_service_test.php`：无数据库依赖的 locale 解析和消息回退回归脚本。
- `admin/src/i18n/**`、`admin/src/install/plugins/i18n.ts`、`admin/src/App.vue`、`admin/src/utils/request/index.ts`、`admin/src/layout/default/components/header/**`：管理后台 i18n、Element Plus locale、请求头和切换入口。
- `admin/src/**/*.{vue,ts}`：迁移管理后台自有界面文案和前端校验提示。
- `pc/i18n/**`、`pc/plugins/i18n.ts`、`pc/app.vue`、`pc/utils/http/index.ts`、`pc/layouts/components/header/**`：PC i18n、SSR 初始化、Element Plus locale、请求头和切换入口。
- `pc/{pages,components,layouts,composables}/**/*.{vue,ts}`：迁移 PC 自有界面文案和前端校验提示。
- `uniapp/src/i18n/**`、`uniapp/src/main.ts`、`uniapp/src/utils/request/index.ts`、`uniapp/src/pages/user_set/user_set.vue`：UniApp i18n、请求头和语言设置入口。
- `uniapp/src/{pages,components,widgets}/**/*.{vue,ts}`：迁移 UniApp 自有界面文案和前端校验提示；不改 `uni_modules` 第三方源码。
- `admin/package.json`、`pc/package.json` 及团队选定包管理器对应的单个锁文件：声明 `vue-i18n@9.1.9`。

## Tasks

### Task 1: 后端请求级语言解析

**Files:**
- Create: `server/app/common/service/LocaleService.php`
- Create: `server/app/common/http/middleware/LocaleMiddleware.php`
- Modify: `server/config/middleware.php`
- Test: `server/tests/locale_service_test.php`

**Interfaces:**
- `LocaleService::resolve(?string $acceptLanguage): string` returns only `zh-CN` or `en-US`.
- `LocaleService::current(): string` returns the active request locale or `zh-CN` outside a request.
- `LocaleService::runWithLocale(string $locale, callable $handler): Response` establishes the request-local locale and restores it in `finally`.
- `LocaleService::translate(string $key, array $replace = [], ?string $locale = null): string` uses the explicit locale or current request locale; missing English keys fall back to Chinese, and keys missing in both catalogs return the generic Chinese error message.
- `LocaleService::translateMessage(string $message, ?string $locale = null): string` maps known legacy Chinese response text to stable message keys and leaves unknown source text intact.
- `LocaleMiddleware::process(Request $request, callable $handler): Response` scopes the resolved locale through Webman's request-local context API and clears it in `finally`; verify that API against the locked Webman 2.1 dependency before implementation.

- [x] **Step 1: Add failing resolver/context checks** for `en-US,en;q=0.9`, `zh-CN,zh;q=0.8`, `en`, empty, malformed and unsupported `fr-FR` headers; assert `runWithLocale('en-US', ...)` exposes English inside the callback and restores the default after both normal return and exception.
- [x] **Step 2: Run `php server/tests/locale_service_test.php`** and confirm it fails because the resolver is not implemented.
- [x] **Step 3: Implement `LocaleService::resolve()` and `runWithLocale()`** with quality ordering, `en`/`zh` regional normalization and `zh-CN` fallback; use Webman's request-local context API, never a process-static mutable locale.
- [x] **Step 4: Register `LocaleMiddleware` in the global middleware list** before API group middleware; verify both `/api` and `/adminapi` enter with the same locale contract.
- [x] **Step 5: Run `php server/tests/locale_service_test.php`** and confirm all resolver assertions pass.
- [x] **Step 6: Commit** as `feat: add request-scoped locale resolution`.

### Task 2: 后端验证文案和 API 消息本地化

**Files:**
- Modify: `server/resource/translations/en/validate.php`
- Create: `server/resource/translations/zh_CN/messages.php`
- Create: `server/resource/translations/en/messages.php`
- Modify: `server/app/common/service/LocaleService.php`
- Modify: `server/app/common/service/JsonService.php`
- Modify: `server/app/common/exception/Handler.php`
- Test: `server/tests/locale_service_test.php`

**Interfaces:**
- `LocaleService::translate(string $key, array $replace = []): string` uses the current request locale and falls back to Chinese for missing keys.
- Existing human-readable response strings continue through `JsonService`; a legacy-message alias table maps inventoried messages to stable keys before translation.

- [x] **Step 1: Add failing message tests** for a stable API message key, a legacy Chinese message alias, placeholder replacement, preservation of a missing placeholder and an unknown-key generic Chinese fallback.
- [x] **Step 2: Run the PHP locale test script** and confirm the new translation assertions fail.
- [x] **Step 3: Fill the English framework validation pack** with English values while preserving every existing source key and placeholder.
- [x] **Step 4: Add matching `messages.php` catalogs** for common validation, system, and API feedback; inventory strings passed to `JsonService` and common `HttpException` paths, then map each user-facing message to a stable key.
- [x] **Step 5: Localize `JsonService` success/fail/throw messages and JSON exceptions in `Handler`** at the response boundary; preserve response keys, types, status codes and business codes.
- [x] **Step 6: Run `php server/tests/locale_service_test.php` and `php -n -l`** on each modified PHP file; confirm tests pass and syntax checks report no errors.
- [x] **Step 7: Commit** as `feat: localize backend validation and API messages`.

### Task 3: 管理后台 i18n 基础和切换

**Files:**
- Create: `admin/src/i18n/index.ts`
- Create: `admin/src/i18n/locales/zh-CN.ts`
- Create: `admin/src/i18n/locales/en-US.ts`
- Create: `admin/src/install/plugins/i18n.ts`
- Create: `admin/src/layout/default/components/header/language-switcher.vue`
- Modify: `admin/src/App.vue`
- Modify: `admin/src/utils/request/index.ts`
- Modify: `admin/src/layout/default/components/header/index.vue`
- Modify: `admin/package.json` and the team-selected package manager lock file only

**Interfaces:**
- `i18n.global.locale.value` is the source of truth for visible locale.
- Storage key is `app_locale`; API request interceptor sends that resolved locale as `Accept-Language`.

- [x] **Step 1: Confirm the team-selected package manager** before dependency changes; add `vue-i18n@9.1.9` to `admin/package.json` and update only its corresponding lock file.
- [x] **Step 2: Create the locale instance and two dictionaries** with `zh-CN` default, saved-choice precedence, browser-language detection on first visit and fallback to Chinese.
- [x] **Step 3: Register the plugin through the existing `admin/src/install/plugins` auto-loader** and bind Element Plus locale in `App.vue` to the current i18n locale.
- [x] **Step 4: Add a header language switcher** that updates the i18n instance and `localStorage` without reloading the application.
- [x] **Step 5: Add `Accept-Language` to the shared Axios request hook** so retries and all API calls use the current locale.
- [ ] **Step 6: Run `npm run type-check` and `npm exec -- vite build`** (do not run the release script); manually verify switching, refresh persistence and Element Plus date/pagination labels.
- [x] **Step 7: Commit** as `feat: add bilingual admin locale switching`.

### Task 4: 管理后台界面词条迁移

**Files:**
- Modify: `admin/src/**/*.{vue,ts}` outside the i18n resource files, including `src/views`, `src/components`, `src/layout` and `src/install`.

- [x] **Step 1: Inventory visible hard-coded text** in admin pages, shared components, route labels, confirmation dialogs, toast messages and client-side validation.
- [x] **Step 2: Replace each inventoried user-facing literal with a namespaced i18n key**, add the same key to both dictionaries, and route visible date/number formatting through the current locale; leave API data and business calculations unchanged.
- [ ] **Step 3: Run `npm run type-check` and `npm exec -- vite build`**; manually inspect login, main navigation, a representative CRUD form, table empty states and error feedback in both languages.
- [x] **Step 4: Commit** as `feat: translate admin interface strings`.

### Task 5: PC i18n、SSR 初始化和切换

**Files:**
- Create: `pc/i18n/locales/zh-CN.ts`
- Create: `pc/i18n/locales/en-US.ts`
- Create: `pc/plugins/i18n.ts`
- Create: `pc/layouts/components/header/language-switcher.vue`
- Modify: `pc/app.vue`
- Modify: `pc/utils/http/index.ts`
- Modify: `pc/layouts/components/header/index.vue`
- Modify: `pc/package.json` and the team-selected package manager lock file only

**Interfaces:**
- Nuxt creates one i18n instance per app/request; it must not share mutable locale across SSR requests.
- Cookie key is `app_locale`; `Accept-Language` is sent by the shared ofetch request interceptor.

- [x] **Step 1: Use the team-selected package manager** to add `vue-i18n@9.1.9` and update only the matching PC lock file.
- [x] **Step 2: Create the Nuxt i18n plugin** with cookie-backed SSR locale initialization, `zh-CN` static-generation default and post-hydration saved-choice or first-visit browser-language application.
- [x] **Step 3: Bind Element Plus locale in `pc/app.vue`** to the per-app i18n instance and add a header switcher that persists the cookie.
- [x] **Step 4: Add the locale header in `pc/utils/http/index.ts`** for all shared API calls.
- [ ] **Step 5: Run `npm exec -- nuxt generate` and `npm exec -- nuxt build`** (do not run `scripts/build.mjs`); inspect SSR/static startup and verify switch, refresh persistence and Element Plus labels.
- [x] **Step 6: Commit** as `feat: add bilingual PC locale switching`.

### Task 6: PC 界面词条迁移

**Files:**
- Modify: `pc/{pages,components,layouts,composables}/**/*.{vue,ts}` and `pc/app.vue`, excluding i18n resources.

- [x] **Step 1: Inventory visible hard-coded text** in public pages, account flows, navigation, dialogs, form validation and feedback.
- [x] **Step 2: Replace inventoried user-facing literals with i18n keys**, add both Chinese and English values, and use the current locale for visible date/number formatting; leave server-provided business content unchanged.
- [ ] **Step 3: Run `npm exec -- nuxt generate` and `npm exec -- nuxt build`**; inspect home, news/article, account forms, empty states and request errors in both locales.
- [x] **Step 4: Commit** as `feat: translate PC interface strings`.

### Task 7: UniApp i18n、语言设置和请求头

**Files:**
- Create: `uniapp/src/i18n/index.ts`
- Create: `uniapp/src/i18n/locales/zh-CN.ts`
- Create: `uniapp/src/i18n/locales/en-US.ts`
- Modify: `uniapp/src/main.ts`
- Modify: `uniapp/src/utils/request/index.ts`
- Modify: `uniapp/src/pages/user_set/user_set.vue`

**Interfaces:**
- UniApp i18n instance is installed in `createApp()` and reads/writes `uni` storage key `app_locale`.
- Request hook sets `options.header['Accept-Language']` from the installed locale; upload calls using the same request hook retain the header.

- [x] **Step 1: Create the locale instance and dictionaries** using the existing `vue-i18n@9.1.9` dependency; detect device language on first visit and persist an explicit selection.
- [x] **Step 2: Install i18n in `uniapp/src/main.ts`** before mounting app routes and expose the same instance to page components.
- [x] **Step 3: Add a language selector to the existing user settings page** and apply changes immediately without logout or navigation reset.
- [x] **Step 4: Add `Accept-Language` in the shared UniApp request hook** and verify regular requests and upload requests inherit it.
- [ ] **Step 5: Run `npm exec -- uni build` for H5 and `npm run build:mp-weixin`**; manually verify language persistence and system component labels on both.
- [x] **Step 6: Commit** as `feat: add bilingual UniApp locale switching`.

### Task 8: UniApp 界面词条迁移

**Files:**
- Modify: `uniapp/src/{pages,components,widgets}/**/*.{vue,ts}`, excluding `uni_modules` and i18n resources.

- [x] **Step 1: Inventory visible literals** in owned pages, custom widgets, settings, account flows, tab/navigation labels, toasts and client-side validation.
- [x] **Step 2: Replace inventoried user-facing strings with i18n keys**, add matching `zh-CN` / `en-US` entries, use locale-aware visible date/number formatting, and pass translated props to third-party components where supported, without editing vendored `uni_modules`.
- [ ] **Step 3: Run `npm exec -- uni build` for H5 and `npm run build:mp-weixin`**; manually inspect home, user settings, login, forms, empty states and error toasts in both locales.
- [x] **Step 4: Commit** as `feat: translate UniApp interface strings`.

### Task 9: End-to-end acceptance

**Files:**
- No new production files; update the design/spec status only if implementation changes an approved decision.

- [x] **Step 1: Run `php server/tests/locale_service_test.php` and PHP syntax checks** for changed server files.
- [ ] **Step 2: Run admin `npm run type-check` and `npm exec -- vite build`; run PC `npm exec -- nuxt generate` and `npm exec -- nuxt build`; run UniApp `npm exec -- uni build` and `npm run build:mp-weixin`**, avoiding release scripts that replace `server/public` directories.
- [ ] **Step 3: Use local HTTP requests with `Accept-Language: en-US` and `zh-CN`** to verify invalid input messages, unchanged response shape and default fallback for unsupported locales.
- [ ] **Step 4: Send consecutive English and Chinese requests through the same running worker** and confirm no locale bleed; verify CORS preflight accepts the requested language header.
- [x] **Step 5: Run `git diff --check` and inspect the final scoped diff**; report static/build results separately from HTTP/runtime acceptance.
- [x] **Step 6: Commit** any final integration fixes as `fix: complete bilingual locale integration`.
