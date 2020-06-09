<?php


namespace Embed\Providers\OEmbed;


class Twitter extends OEmbedImplementation
{
    /**
     * {@inheritdoc}
     */
    public static function getEndPoint()
    {
        return 'https://publish.twitter.com/oembed';
    }

    /**
     * {@inheritdoc}
     */
    public static function getPatterns()
    {
        return [
            'https://twitter.com/*/status/*',
            'https://*.twitter.com/*/status/*'
        ];
    }
}