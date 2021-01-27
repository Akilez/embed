<?php


namespace Embed\Adapters;

use Embed\Providers\Api;

class Instagram extends Webpage implements AdapterInterface
{
    public function init ()
    {
        $this->addProvider('instagram', new Api\Instagram());

        $this->run();
    }
}