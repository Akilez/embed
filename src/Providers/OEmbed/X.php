<?php


namespace Embed\Providers\OEmbed;


class X extends OEmbedImplementation
{
    /**
     * {@inheritdoc}
     */
    public static function getEndPoint()
    {
        return 'https://publish.x.com/oembed';
    }

    /**
     * {@inheritdoc}
     */
    public static function getPatterns()
    {
        return [
            'https://x.com/*/status/*',
            'https://*.x.com/*/status/*'
        ];
    }
}
