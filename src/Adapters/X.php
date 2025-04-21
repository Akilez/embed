<?php


namespace Embed\Adapters;


use Embed\Request;

class X extends Webpage implements AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function check(Request $request)
    {
        return $request->match([
                'https://x.com/*/status/*',
                'https://*.x.com/*/status/*'
            ]);
    }

    public function init ()
    {
        $this->run();
    }
}
