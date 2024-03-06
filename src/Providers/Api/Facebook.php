<?php
namespace Embed\Providers\Api;

use Embed\Bag;
use Embed\Providers\Provider;
use Embed\Providers\ProviderInterface;
use Embed\Request;
use Embed\Url;

/**
 * Provider to use the API of facebook
 */
class Facebook extends Provider implements ProviderInterface
{
    protected $config = [
        'key' => null,
    ];

    private $isEmbeddable = false;

        /**
         * {@inheritdoc}
         */
        public function run()
        {
            if ($this->request->getPath() == '/login' && $this->request->hasQueryParameter('next')) {
                $id = $this->getId($url = new Url($this->request->getQueryParameter('next')));
                $this->bag->set('request_url', $url->getUrl());
            } else {
                $id = $this->getId($this->request);
            }
            if ($id) {
                if ($accessToken = $this->getAccessToken()) {
                    $api = $this->request
                        ->withUrl('https://graph.facebook.com/v19.0/oembed_post')
                        ->withQueryParameter('url', isset($url) ? $url->getUrl() : $this->request->getUrl())
                        ->withQueryParameter('access_token', $accessToken);

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
            if ($url->hasQueryParameter('story_fbid')) {
                $this->isEmbeddable = true;

                return $url->getQueryParameter('story_fbid');
            }

            if ($url->hasQueryParameter('fbid')) {
                return $url->getQueryParameter('fbid');
            }

            if ($url->hasQueryParameter('id')) {
                return $url->getQueryParameter('id');
            }

            if ($url->getDirectoryPosition(0) === 'events') {
                $this->isEmbeddable = true;

                return $url->getDirectoryPosition(1);
            }

            if ($url->getDirectoryPosition(0) === 'pages') {
                return $url->getDirectoryPosition(2);
            }

            if ($url->getDirectoryPosition(1) === 'posts') {
                $this->isEmbeddable = true;

                return $url->getDirectoryPosition(2);
            }

            if ($url->getDirectoryPosition(2) === 'posts') {
                $this->isEmbeddable = true;

                return $url->getDirectoryPosition(3);
            }

            if ($url->getDirectoryPosition(1) === 'videos') {
                $this->isEmbeddable = true;

                return $url->getDirectoryPosition(3);
            }

            if ($url->getDirectoryPosition(1) === 'photos') {
                $this->isEmbeddable = true;

                return $url->getDirectoryPosition(3);
            }

            return $url->getDirectoryPosition(0);
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
            if ($this->isEmbeddable) {
                return $this->bag->get('html');
            }
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

    /**
     * @return mixed
     */
    protected function getAccessToken()
    {
        $api = $this->request
            ->withUrl('https://graph.facebook.com/oauth/access_token')
            ->withQueryParameter('grant_type', 'client_credentials')
            ->withQueryParameter('client_id', $this->config['client_id'])
            ->withQueryParameter('client_secret', $this->config['client_secret']);

        if ($json = $api->getJsonContent()) {
            return $json['access_token'];
        }

        return false;
    }
}
