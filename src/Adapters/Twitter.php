<?php


namespace Embed\Adapters;


use Embed\Request;

class Twitter extends Webpage implements AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function check(Request $request)
    {
        return $request->match([
                'https://twitter.com/*/status/*',
                'https://*.twitter.com/*/status/*'
            ]);
    }

    public function init ()
    {
        $this->run();
    }
}