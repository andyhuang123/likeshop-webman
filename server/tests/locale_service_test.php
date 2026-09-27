<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use app\common\service\LocaleService;
use app\common\service\JsonService;
use app\common\exception\HttpException;
use app\common\exception\Handler;
use app\common\http\middleware\LocaleMiddleware;
use Webman\Http\Request;
use support\Response;

function expectSame(mixed $expected, mixed $actual, string $case): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($case . ': expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}

$cases = [
    ['en-US,en;q=0.9', 'en-US'],
    ['zh-CN,zh;q=0.8', 'zh-CN'],
    ['en', 'en-US'],
    ['zh', 'zh-CN'],
    ['fr-FR', 'zh-CN'],
    ['', 'zh-CN'],
    ['???;q=invalid', 'zh-CN'],
    ['zh-CN;q=0.2,en-US;q=0.9', 'en-US'],
    ['en-US;q=0,zh-CN;q=0.5', 'zh-CN'],
];

foreach ($cases as [$header, $expected]) {
    expectSame($expected, LocaleService::resolve($header), 'resolve ' . var_export($header, true));
}

$response = LocaleService::runWithLocale('en-US', static function (): Response {
    expectSame('en-US', LocaleService::current(), 'locale inside callback');
    return new Response(200, [], 'ok');
});
expectSame(true, $response instanceof Response, 'callback response');
expectSame('zh-CN', LocaleService::current(), 'default locale after callback');

try {
    LocaleService::runWithLocale('en-US', static function (): Response {
        expectSame('en-US', LocaleService::current(), 'locale before exception');
        throw new RuntimeException('expected exception');
    });
    throw new RuntimeException('runWithLocale should rethrow the callback exception');
} catch (RuntimeException $exception) {
    expectSame('expected exception', $exception->getMessage(), 'callback exception is preserved');
}
expectSame('zh-CN', LocaleService::current(), 'default locale after exception');

$request = new Request("GET / HTTP/1.1\r\nAccept-Language: en-US\r\n\r\n");
$middlewareResponse = (new LocaleMiddleware())->process($request, static function (Request $request): Response {
    expectSame('en-US', LocaleService::current(), 'locale inside middleware');
    return new Response(200, [], 'ok');
});
expectSame(true, $middlewareResponse instanceof Response, 'middleware response');
expectSame('zh-CN', LocaleService::current(), 'default locale after middleware');

$middleware = require __DIR__ . '/../config/middleware.php';
$globalMiddleware = $middleware[''];
$localePosition = array_search(LocaleMiddleware::class, $globalMiddleware, true);
$allowPosition = array_search(app\common\http\middleware\AllowMiddleware::class, $globalMiddleware, true);
expectSame(true, $localePosition !== false && $allowPosition !== false && $localePosition < $allowPosition, 'locale middleware runs before global middleware');

$chineseValidation = require __DIR__ . '/../resource/translations/zh_CN/validate.php';
$englishValidation = require __DIR__ . '/../resource/translations/en/validate.php';
$chineseMessages = require __DIR__ . '/../resource/translations/zh_CN/messages.php';
$englishMessages = require __DIR__ . '/../resource/translations/en/messages.php';
expectSame(array_keys($chineseValidation), array_keys($englishValidation), 'validation language pack keys');
$chineseMessageKeys = array_diff(array_keys($chineseMessages), ['_aliases']);
$englishMessageKeys = array_diff(array_keys($englishMessages), ['validation.attributes']);
expectSame([], array_values(array_diff($chineseMessageKeys, $englishMessageKeys)), 'English catalog covers every Chinese message key');
expectSame([], array_values(array_diff($englishMessageKeys, $chineseMessageKeys)), 'Chinese catalog covers every English message key');
foreach ($chineseMessageKeys as $key) {
    if (!is_string($chineseMessages[$key]) || !is_string($englishMessages[$key])) {
        continue;
    }
    expectSame(0, preg_match('/\p{Han}/u', $englishMessages[$key]), 'English message text for ' . $key);
    preg_match_all('/\{:[a-zA-Z0-9_]+\}|:[a-zA-Z0-9_]+/u', $chineseMessages[$key], $sourcePlaceholders);
    preg_match_all('/\{:[a-zA-Z0-9_]+\}|:[a-zA-Z0-9_]+/u', $englishMessages[$key], $targetPlaceholders);
    expectSame($sourcePlaceholders[0], $targetPlaceholders[0], 'message placeholders for ' . $key);
}
foreach ($chineseValidation as $key => $template) {
    expectSame(0, preg_match('/\p{Han}/u', $englishValidation[$key]), 'English validation text for ' . $key);
    preg_match_all('/\{:[a-zA-Z0-9_]+\}|:[a-zA-Z0-9_]+/u', $template, $sourcePlaceholders);
    preg_match_all('/\{:[a-zA-Z0-9_]+\}|:[a-zA-Z0-9_]+/u', $englishValidation[$key], $targetPlaceholders);
    expectSame($sourcePlaceholders[0], $targetPlaceholders[0], 'validation placeholders for ' . $key);
}

expectSame('A server error occurred.', LocaleService::translate('system.server_error', [], 'en-US'), 'stable English message key');
expectSame('You do not have permission to access or perform this action.', LocaleService::translateMessage('权限不足，无法访问或操作', 'en-US'), 'legacy API message alias');
expectSame('File size must not exceed 5 MB.', LocaleService::translate('validation.file_size_limit', ['max' => 5], 'en-US'), 'message placeholder replacement');
expectSame('File size must not exceed :max MB.', LocaleService::translate('validation.file_size_limit', [], 'en-US'), 'unprovided placeholder preservation');
expectSame('The account field is required.', LocaleService::translateMessage('account不能为空', 'en-US'), 'framework validation template translation');
expectSame('The title field is required.', LocaleService::translateMessage('标题不能为空', 'en-US'), 'Chinese validation field label translation');
expectSame('The password was entered incorrectly 5 consecutive times. Try again in 10 minutes.', LocaleService::translateMessage('密码连续5次输入错误，请10分钟后重试', 'en-US'), 'dynamic legacy message translation');
expectSame('Please enter the rule name.', LocaleService::translateMessage('请输入规则名称', 'en-US'), 'custom validation prompt translation');
expectSame('The inventory must be at least 0.', LocaleService::translateMessage('库存必须大于等于0', 'en-US'), 'custom numeric validation translation');
expectSame('The inventory must be at least 0.', LocaleService::translateMessage('库存不能小于0', 'en-US'), 'custom minimum validation translation');
expectSame('The title length must be between 1 and 255 characters.', LocaleService::translateMessage('标题长度须在1-255位字符', 'en-US'), 'custom length validation translation');
$customValidationMessages = [
    '表字段信息缺失' => 'Table field information is required.',
    '表id缺失' => 'Table ID is required.',
    '调整类型错误' => 'The adjustment type is invalid.',
    '调整余额必须大于零' => 'The adjustment amount must be greater than zero.',
    '订单参数缺失' => 'The order parameter is missing.',
    '多处登录状态值为误' => 'The multi-session login setting is invalid.',
    '岗位状态值错误' => 'The job status is invalid.',
    '规格类型不正确' => 'The specification type is invalid.',
    '名称须在1-16位字符' => 'The name must be between 1 and 16 characters.',
    '排序值需大于或等于0' => 'The sort order must be at least 0.',
    '排序最大不能超过五位数' => 'The sort order must not exceed five digits.',
    '启用状态值错误' => 'The enabled status is invalid.',
    '启用状态值有误' => 'The enabled status is invalid.',
    '请上传登录页广告图' => 'Please upload the login page banner.',
    '请上传前台logo' => 'Please upload the storefront logo.',
    '请上传网站图标' => 'Please upload the website icon.',
    '请上传网站logo' => 'Please upload the website logo.',
    '请上传用户默认头像' => 'Please upload the default user avatar.',
    '请上传PC端logo' => 'Please upload the PC logo.',
    '权限格式错误' => 'The permission format is invalid.',
    '权限字符不能超过100个字符' => 'The permission key must not exceed 100 characters.',
    '所属栏目必须存在' => 'The article category is required.',
    '系统取消待付款订单时间未填写' => 'The unpaid order cancellation interval is required.',
    '系统取消待付款订单状态值有误' => 'The unpaid order cancellation status is invalid.',
    '系统自动核销订单时间未填写' => 'The automatic order verification interval is required.',
    '系统自动核销订单状态值有误' => 'The automatic order verification status is invalid.',
    '限领数量必须为整数' => 'The claim limit must be an integer.',
    '账号须为3-12位之间' => 'The account must be between 3 and 12 characters.',
    '折扣率必须在0-10之间' => 'The discount rate must be between 0 and 10.',
    '支付参数缺失' => 'The payment parameter is missing.',
    '终端参数缺少' => 'The terminal parameter is missing.',
    '终端参数缺失' => 'The terminal parameter is required.',
    '终端参数状态值不正确' => 'The terminal status is invalid.',
    '字典类型缺失' => 'The dictionary type is missing.',
    '字典名称长度须在1~255位字符' => 'The dictionary name must be between 1 and 255 characters.',
    'access_token缺少' => 'The access token is missing.',
    'opendid缺少' => 'The OpenID is required.',
];
foreach ($customValidationMessages as $source => $target) {
    expectSame($target, LocaleService::translateMessage($source, 'en-US'), 'custom validation translation: ' . $source);
}
$apiErrors = [
    '删除失败' => 'Deletion failed.',
    '盲盒不存在' => 'The blind box was not found.',
    '订单不存在' => 'The order was not found.',
    '用户不存在' => 'The user was not found.',
    '原密码不正确' => 'The current password is incorrect.',
    '验证码错误' => 'The verification code is incorrect.',
    '该手机号已被使用' => 'This phone number is already in use.',
    '订单已支付' => 'The order has already been paid.',
    '一级菜单名称字数不能超过4个字符' => 'The first-level menu name must not exceed 4 characters.',
    '上传图片不允许上传png文件' => 'Image uploads do not support the png file type.',
    '上传文件不允许上传pdf文件' => 'File uploads do not support the pdf file type.',
    '上传视频不允许上传mov文件' => 'Video uploads do not support the mov file type.',
    '不允许上传exe后缀文件' => 'The exe file extension is not allowed.',
    '保存发布菜单失败{"errcode":1}' => 'Failed to save and publish the menu: {"errcode":1}',
    '头像保存失败:storage failed' => 'Failed to save the avatar: storage failed',
    '微信:PAY_ERROR-timeout' => 'WeChat error: PAY_ERROR-timeout',
    '未找到存储引擎类: local' => 'Storage engine not found: local',
    '腾讯云短信错误：request failed' => 'Tencent Cloud SMS error: request failed',
    '阿里云短信错误：request failed' => 'Alibaba Cloud SMS error: request failed',
    '获取jssdk失败:network error' => 'Failed to retrieve the JS SDK configuration: network error',
];
foreach ($apiErrors as $source => $target) {
    expectSame($target, LocaleService::translateMessage($source, 'en-US'), 'API error translation: ' . $source);
}
expectSame('服务器错误!', LocaleService::translate('missing.translation.key', [], 'en-US'), 'unknown key fallback');
expectSame('unmapped source text', LocaleService::translateMessage('unmapped source text', 'en-US'), 'unknown message preservation');

$localizedResponse = LocaleService::runWithLocale('en-US', static fn(): Response => JsonService::fail('权限不足，无法访问或操作', ['unchanged' => true], 47, 1, 403));
$localizedPayload = json_decode($localizedResponse->rawBody(), true, 512, JSON_THROW_ON_ERROR);
expectSame(['code' => 47, 'show' => 1, 'msg' => 'You do not have permission to access or perform this action.', 'data' => ['unchanged' => true]], $localizedPayload, 'API response contract with localized message');
expectSame(403, $localizedResponse->getStatusCode(), 'localized API response HTTP status');

$localizedSuccess = LocaleService::runWithLocale('en-US', static fn(): Response => JsonService::success('操作成功', [], 1, 0, 201));
$localizedSuccessPayload = json_decode($localizedSuccess->rawBody(), true, 512, JSON_THROW_ON_ERROR);
expectSame(['code' => 1, 'show' => 0, 'msg' => 'Operation completed successfully.', 'data' => []], $localizedSuccessPayload, 'localized success response contract');
expectSame(201, $localizedSuccess->getStatusCode(), 'localized success HTTP status');

try {
    LocaleService::runWithLocale('en-US', static function (): Response {
        JsonService::throw('权限不足，无法访问或操作', ['unchanged' => true], 47, 1);
    });
    throw new RuntimeException('JsonService::throw should raise HttpException');
} catch (HttpException $exception) {
    $localizedExceptionPayload = json_decode($exception->getResponse()->rawBody(), true, 512, JSON_THROW_ON_ERROR);
    expectSame(['code' => 47, 'show' => 1, 'msg' => 'You do not have permission to access or perform this action.', 'data' => ['unchanged' => true]], $localizedExceptionPayload, 'throw response contract with localized message');
}

$handlerRequest = new Request("GET /api/test HTTP/1.1\r\nAccept: application/json\r\nAccept-Language: en-US\r\n\r\n");
$serverErrorResponse = (new Handler(null, false))->render($handlerRequest, new RuntimeException('internal detail'));
$serverErrorPayload = json_decode($serverErrorResponse->rawBody(), true, 512, JSON_THROW_ON_ERROR);
expectSame('A server error occurred.', $serverErrorPayload['msg'], 'JSON exception handler message localization');
expectSame(['code', 'msg', 'show'], array_keys($serverErrorPayload), 'JSON exception response shape');
$fallbackRequest = new Request("GET /api/test HTTP/1.1\r\nAccept: application/json\r\nAccept-Language: fr-FR\r\n\r\n");
$fallbackResponse = (new Handler(null, false))->render($fallbackRequest, new RuntimeException('internal detail'));
$fallbackPayload = json_decode($fallbackResponse->rawBody(), true, 512, JSON_THROW_ON_ERROR);
expectSame('服务器错误!', $fallbackPayload['msg'], 'JSON exception handler locale fallback');

echo "locale resolver and request-context checks passed\n";
