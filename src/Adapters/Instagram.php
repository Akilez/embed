<?php


namespace Embed\Adapters;


class Instagram extends Webpage implements AdapterInterface
{
    public function init ()
    {
        $this->run();
    }
}