<?php


namespace Embed\Providers\OEmbed;


class Tiktok extends OEmbedImplementation
{
    /**
     * {@inheritdoc}
     */
    public static function getEndPoint()
    {
        return 'https://www.tiktok.com/oembed';
    }

    /**
     * {@inheritdoc}
     */
    public static function getPatterns()
    {
        return [
            'https://www.tiktok.com/*/video/*',
            'https://*.tiktok.com/*/video/*'
        ];
    }
}