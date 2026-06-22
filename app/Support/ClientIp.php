<?php

namespace App\Support;

use Illuminate\Http\Request;

class ClientIp
{
    public static function resolve(Request $request): ?string
    {
        $candidates = [];

        foreach (['CF-Connecting-IP', 'X-Real-IP', 'X-Forwarded-For'] as $header) {
            $value = $request->header($header);

            if (! $value) {
                continue;
            }

            foreach (explode(',', $value) as $part) {
                $part = trim($part);

                if ($part !== '') {
                    $candidates[] = $part;
                }
            }
        }

        $candidates[] = $request->ip();

        foreach ($candidates as $candidate) {
            $normalized = self::normalize($candidate);

            if ($normalized && filter_var($normalized, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $normalized;
            }
        }

        $fallback = self::normalize($request->ip());

        return $fallback ?: null;
    }

    public static function normalize(?string $ip): ?string
    {
        if ($ip === null || $ip === '') {
            return null;
        }

        $ip = trim($ip);

        if (str_starts_with(strtolower($ip), '::ffff:')) {
            $ipv4 = substr($ip, 7);

            if (filter_var($ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $ipv4;
            }
        }

        return $ip;
    }
}
