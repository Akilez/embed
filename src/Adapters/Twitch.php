<?php
/**
 * Adapter to provide information from any facebook page using its graph API
 */
namespace Embed\Adapters;

use Embed\Request;
use Embed\Providers\Api;

class Twitch extends Webpage implements AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function check(Request $request)
    {
        return $request->isValid() && $request->match([
            'https://www.twitch.tv/*',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        //$this->addProvider('twitch', new Api\Twitch());

        parent::run();
    }
}
