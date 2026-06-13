<?php

if (! function_exists('versioned_asset')) {
    function versioned_asset(string $path): string
    {
        $normalizedPath = ltrim($path, '/');
        $fullPath = public_path($normalizedPath);
        $version = is_file($fullPath) ? filemtime($fullPath) : config('app.asset_version', '1');

        return asset($normalizedPath) . '?v=' . $version;
    }
}
