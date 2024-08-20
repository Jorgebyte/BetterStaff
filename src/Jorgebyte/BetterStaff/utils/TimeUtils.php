<?php

namespace Jorgebyte\BetterStaff\utils;

class TimeUtils
{
    public static function parseTime(string $timeString): int|false
    {
        $multipliers = [
            's' => 1,
            'm' => 60,
            'h' => 3600,
            'd' => 86400
        ];

        $unit = strtolower(substr($timeString, -1));
        $value = intval(substr($timeString, 0, -1));

        return $multipliers[$unit] ?? false ? $value * $multipliers[$unit] : false;
    }

    public static function formatDuration(int $seconds): string
    {
        $units = [
            'd' => floor($seconds / 86400),
            'h' => floor(($seconds % 86400) / 3600),
            'm' => floor(($seconds % 3600) / 60),
            's' => $seconds % 60
        ];

        $formattedDuration = [];
        foreach ($units as $unit => $value) {
            if ($value > 0) {
                $formattedDuration[] = "$value " . ($unit == 's' ? 'second' . ($value !== 1 ? 's' : '') : $unit);
            }
        }
        return implode(' ', $formattedDuration);
    }
}