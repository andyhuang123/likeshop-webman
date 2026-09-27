<?php

declare(strict_types=1);

namespace app\common\service;

use support\Context;
use support\Response;

class LocaleService
{
    private const CONTEXT_KEY = 'app.locale';
    private const DEFAULT_LOCALE = 'zh-CN';

    public static function resolve(?string $acceptLanguage): string
    {
        if ($acceptLanguage === null || trim($acceptLanguage) === '') {
            return self::DEFAULT_LOCALE;
        }

        $locales = [];
        foreach (explode(',', $acceptLanguage) as $position => $item) {
            $parts = explode(';', trim($item));
            $locale = self::normalizeLocaleTag(trim(array_shift($parts)));
            if ($locale === null) {
                continue;
            }

            $quality = 1.0;
            $valid = true;
            foreach ($parts as $parameter) {
                if (preg_match('/^\s*q\s*=\s*(.*?)\s*$/i', $parameter, $matches)) {
                    if (!preg_match('/^(?:0(?:\.\d{0,3})?|1(?:\.0{0,3})?)$/', $matches[1])) {
                        $valid = false;
                        break;
                    }
                    $quality = (float)$matches[1];
                }
            }

            if ($valid && $quality > 0) {
                $locales[] = ['locale' => $locale, 'quality' => $quality, 'position' => $position];
            }
        }

        usort($locales, static function (array $left, array $right): int {
            return ($right['quality'] <=> $left['quality']) ?: ($left['position'] <=> $right['position']);
        });

        return $locales[0]['locale'] ?? self::DEFAULT_LOCALE;
    }

    public static function current(): string
    {
        $locale = Context::get(self::CONTEXT_KEY);
        return in_array($locale, ['zh-CN', 'en-US'], true) ? $locale : self::DEFAULT_LOCALE;
    }

    public static function runWithLocale(string $locale, callable $handler): Response
    {
        $hadLocale = Context::has(self::CONTEXT_KEY);
        $previousLocale = Context::get(self::CONTEXT_KEY);
        Context::set(self::CONTEXT_KEY, self::resolve($locale));

        try {
            return $handler();
        } finally {
            Context::set(self::CONTEXT_KEY, $hadLocale ? $previousLocale : null);
        }
    }

    private static function normalizeLocaleTag(string $locale): ?string
    {
        if (preg_match('/^en(?:-US)?$/i', $locale)) {
            return 'en-US';
        }
        if (preg_match('/^zh(?:-CN)?$/i', $locale)) {
            return 'zh-CN';
        }
        return null;
    }
}
