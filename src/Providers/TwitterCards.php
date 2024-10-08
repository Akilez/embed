<?php
namespace Embed\Providers;

use Embed\Utils;

/**
 * Generic twitter cards provider.
 *
 * Load the twitter cards data of an url and store it
 */
class TwitterCards extends Provider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!($html = $this->request->getHtmlContent())) {
            return false;
        }

        foreach (Utils::getMetas($html) as $meta) {
            list($name, $value) = $meta;

            if (strpos($name, 'twitter:') === 0) {
                $name = substr($name, 8);

                if ($name === 'image') {
                    $this->bag->add('images', $value);
                } else {
                    $this->bag->set($name, $value);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->bag->get('title');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->bag->get('description');
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        $type = $this->bag->get('card');

        if (strpos($type, ':') !== false) {
            $type = substr(strrchr($type, ':'), 1);
        }

        switch ($type) {
            case 'video':
            case 'photo':
            case 'link':
            case 'rich':
                return $type;

            case 'player':
                return 'video';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        if ($this->bag->has('player')) {
            $src = $this->bag->get('player');
            if (preg_match('/parent=meta\.tag/', $src)) {
                $src = preg_replace_callback('/parent=meta\.tag/', [
                    $this,
                    'addFrameAncestors',
                ], $src);
            }

            return Utils::iframe($src, $this->getWidth(), $this->getHeight());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->bag->get('url');
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorName()
    {
        return $this->bag->get('creator');
    }

    /**
     * {@inheritdoc}
     */
    public function getImagesUrls()
    {
        return (array) $this->bag->get('images') ?: [];
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return $this->bag->get('player:width');
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return $this->bag->get('player:height');
    }

    protected function addFrameAncestors($parentString)
    {
        $ancestors = [];
        foreach ($this->config['frame_ancestors'] as $frame_ancestor) {
            $ancestors[] =  sprintf('parent=%s', $frame_ancestor);
        }

        return implode('&', $ancestors);
    }
}
