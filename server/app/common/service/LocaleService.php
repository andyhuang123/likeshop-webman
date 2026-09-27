<?php

declare(strict_types=1);

namespace app\common\service;

use support\Context;
use Webman\Http\Response;

class LocaleService
{
    private const CONTEXT_KEY = 'app.locale';
    private const DEFAULT_LOCALE = 'zh-CN';

    private static array $messageCatalogs = [];

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

    public static function translate(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = self::resolve($locale ?? self::current());
        $message = self::messages($locale)[$key] ?? null;
        if (!is_string($message) && $locale !== self::DEFAULT_LOCALE) {
            $message = self::messages(self::DEFAULT_LOCALE)[$key] ?? null;
        }
        if (!is_string($message)) {
            $message = self::messages(self::DEFAULT_LOCALE)['system.server_error'] ?? '服务器错误!';
        }

        return self::replacePlaceholders($message, $replace);
    }

    public static function translateMessage(string $message, ?string $locale = null): string
    {
        $locale = self::resolve($locale ?? self::current());
        $aliases = self::messages(self::DEFAULT_LOCALE)['_aliases'] ?? [];
        if (isset($aliases[$message])) {
            return self::translate($aliases[$message], [], $locale);
        }

        foreach ($aliases as $source => $key) {
            if (!is_string($source) || !is_string($key) || !str_contains($source, ':')) {
                continue;
            }
            $replace = self::matchTemplate($source, $message);
            if ($replace !== null) {
                return self::translate($key, self::translateAttributes($replace, $locale), $locale);
            }
        }

        if ($locale === 'en-US') {
            if (str_contains($message, ';')) {
                return implode(';', array_map(
                    static fn(string $part): string => self::translateMessage($part, $locale),
                    explode(';', $message)
                ));
            }
            $translated = self::translateValidationMessage($message);
            if ($translated !== null) {
                return $translated;
            }
        }

        return $message;
    }

    private static function messages(string $locale): array
    {
        if (!isset(self::$messageCatalogs[$locale])) {
            $directory = $locale === 'en-US' ? 'en' : 'zh_CN';
            $path = dirname(__DIR__, 3) . '/resource/translations/' . $directory . '/messages.php';
            self::$messageCatalogs[$locale] = is_file($path) ? require $path : [];
        }
        return self::$messageCatalogs[$locale];
    }

    private static function translateValidationMessage(string $message): ?string
    {
        $root = dirname(__DIR__, 3) . '/resource/translations';
        static $source;
        static $target;
        $source ??= require $root . '/zh_CN/validate.php';
        $target ??= require $root . '/en/validate.php';
        foreach ($source as $key => $template) {
            if (!isset($target[$key]) || !is_string($template) || !is_string($target[$key])) {
                continue;
            }
            $replace = self::matchTemplate($template, $message);
            if ($replace !== null) {
                return self::replacePlaceholders($target[$key], self::translateAttributes($replace, 'en-US'));
            }
        }
        return null;
    }

    private static function translateAttributes(array $replace, string $locale): array
    {
        if ($locale === 'en-US' && isset($replace['attribute'])) {
            $attributes = self::messages('en-US')['validation.attributes'] ?? [];
            $replace['attribute'] = $attributes[$replace['attribute']] ?? $replace['attribute'];
        }
        return $replace;
    }

    private static function matchTemplate(string $template, string $message): ?array
    {
        preg_match_all('/\{:[a-zA-Z0-9_]+\}|:[a-zA-Z0-9_]+/u', $template, $tokens, PREG_OFFSET_CAPTURE);
        $pattern = '';
        $offset = 0;
        foreach ($tokens[0] as [$token, $position]) {
            $pattern .= preg_quote(substr($template, $offset, $position - $offset), '~') . '(.+?)';
            $offset = $position + strlen($token);
        }
        $pattern .= preg_quote(substr($template, $offset), '~');

        if (!preg_match('~^' . $pattern . '$~u', $message, $matches)) {
            return null;
        }

        $replace = [];
        foreach ($tokens[0] as $index => [$token]) {
            $replace[trim($token, '{}:')] = $matches[$index + 1];
        }
        return $replace;
    }

    private static function replacePlaceholders(string $message, array $replace): string
    {
        $values = [];
        foreach ($replace as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $values[trim((string)$key, '{}:')] = (string)$value;
            }
        }

        return preg_replace_callback(
            '/\{:(\w+)\}|:(\w+)/u',
            static function (array $matches) use ($values): string {
                $key = $matches[1] !== '' ? $matches[1] : $matches[2];
                return $values[$key] ?? $matches[0];
            },
            $message
        ) ?? $message;
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
