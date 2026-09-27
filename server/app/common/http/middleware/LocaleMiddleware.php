<?php

declare(strict_types=1);

namespace app\common\http\middleware;

use app\common\service\LocaleService;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class LocaleMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $handler): Response
    {
        return LocaleService::runWithLocale(
            LocaleService::resolve($request->header('accept-language')),
            static fn(): Response => $handler($request)
        );
    }
}
