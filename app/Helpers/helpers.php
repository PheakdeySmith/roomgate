<?php

if (!function_exists('getYouTubeId')) {
    /**
     * Extracts the YouTube video ID from a URL.
     *
     * @param string $url The YouTube URL.
     * @return string|null The video ID or null if not found.
     */
    function getYouTubeId($url)
    {
        if (empty($url)) {
            return null;
        }
        preg_match('/(v=|\/v\/|youtu\.be\/|embed\/)([a-zA-Z0-9_-]+)/', $url, $matches);
        return $matches[2] ?? null;
    }
}