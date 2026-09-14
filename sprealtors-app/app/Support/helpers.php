<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Read a site setting, falling back to the configured default.
     */
    function setting(string $key, ?string $default = null): ?string
    {
        return Setting::get($key, $default ?? (Setting::defaults()[$key] ?? null));
    }
}

if (! function_exists('whatsapp_url')) {
    /**
     * Build a wa.me link for the business number with an optional prefilled message.
     */
    function whatsapp_url(?string $message = null): string
    {
        $number = preg_replace('/\D/', '', (string) setting('whatsapp'));
        $url = 'https://wa.me/'.$number;

        if ($message) {
            $url .= '?text='.rawurlencode($message);
        }

        return $url;
    }
}

if (! function_exists('tel_url')) {
    /**
     * Build a tel: link for the business phone number.
     */
    function tel_url(): string
    {
        return 'tel:'.preg_replace('/[^+0-9]/', '', (string) setting('phone'));
    }
}
