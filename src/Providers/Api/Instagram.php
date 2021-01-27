<?php
namespace Embed\Providers\Api;

use Embed\Providers\Provider;
use Embed\Providers\ProviderInterface;
use Embed\Url;

/**
 * Provider to use the API of facebook
 */
class Instagram extends Provider implements ProviderInterface
{
    protected $config = [
        'key' => null,
    ];

    private $isEmbeddable = true;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (($id = $this->getId($this->request))) {
            if ($this->config['key']) {
                $api = $this->request
                    ->withUrl('https://graph.facebook.com/v9.0/instagram_oembed')
                    ->withQueryParameter('url', $this->request->getURL())
                    ->withQueryParameter('access_token', $this->config['key']);

                if ($json = $api->getJsonContent()) {
                    $this->bag->set($json);
                }
            }

            $this->bag->set('id', $id);
        }
    }

    /**
     * Returns the id found in a facebook url
     *
     * @param Url $url
     *
     * @return string
     */
    private function getId(Url $url)
    {
        return $url->getDirectoryPosition(1);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->bag->get('name');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->bag->get('description') ?: $this->bag->get('about');
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        if ($this->isEmbeddable) {
            return 'rich';
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
    public function getCode()
    {
        return $this->bag->get('html');
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        if ($this->isEmbeddable) {
            return 500;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderName()
    {
        return 'Facebook';
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorName()
    {
        return $this->bag->get('username');
    }

    /**
     * {@inheritdoc}
     */
    public function getImagesUrls()
    {
        $images = [];

        if (($cover = $this->bag->get('cover[source]'))) {
            $images[] = $cover;
        }

        if (($id = $this->bag->get('id'))) {
            $images[] = 'https://graph.facebook.com/'.$id.'/picture';
        }

        return $images;
    }
}
