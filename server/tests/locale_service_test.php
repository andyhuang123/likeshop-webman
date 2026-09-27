<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use app\common\service\LocaleService;
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

echo "locale resolver and request-context checks passed\n";
