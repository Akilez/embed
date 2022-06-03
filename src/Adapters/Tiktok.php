<?php


namespace Embed\Adapters;


use Embed\Request;

class Tiktok extends Webpage implements AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function check(Request $request)
    {
        return $request->match([
                'https://www.tiktok.com/*/video/*',
                'https://*.tiktok.com/*/video/*'
            ]);
    }

    public function init ()
    {
        $this->run();
    }
}