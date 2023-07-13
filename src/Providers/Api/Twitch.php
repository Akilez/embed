<?php
namespace Embed\Providers\Api;

use Embed\Bag;
use Embed\Providers\Provider;
use Embed\Providers\ProviderInterface;
use Embed\Request;
use Embed\RequestResolvers\Curl;
use Embed\Url;

/**
 * Provider to use the API of facebook
 */
class Twitch extends Provider implements ProviderInterface
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
            $path = $this->request->getSlicePath(0);

            if ($accessToken = $this->getAccessToken()) {
                $request = new Request($this->request->getUrl(), Curl::class, [
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $accessToken,
                        'Client-Id: ' . $this->config['client_id'],
                    ]
                ]);

                if (isset ($path[1]) && $path[1] == 'clip') {
                    if (isset($path[2])) {
                        $api = $request
                            ->withUrl('https://api.twitch.tv/helix/clips')
                            ->withQueryParameter('id', $path[2]);
                    }
                } else {
                    switch ($path[0]) {
                        case 'videos':
                            $api = $request
                                ->withUrl('https://api.twitch.tv/helix/videos')
                                ->withQueryParameter('id', $path[1]);
                            break;
                        default:
                            $api = $request
                                ->withUrl('https://api.twitch.tv/helix/streams')
                                ->withQueryParameter('user_id', $path[0]);
                            break;
                    }
                }

                if ($json = $api->getJsonContent()) {
                    $this->bag->set($json);
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
            return $this->bag->get('description') ?: $this->bag->get('game_name');
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
            return 'Twitch';
        }

        /**
         * {@inheritdoc}
         */
        public function getAuthorName()
        {
            return $this->bag->get('user_name') ?: $this->bag->get('broadcaster_name');
        }

        /**
         * {@inheritdoc}
         */
        public function getImagesUrls()
        {
            $images = [];

            if (($thumbnail = $this->bag->get('thumbnail_url'))) {
                $images[] = $thumbnail;
            }

            return $images;
        }

    /**
     * @return mixed
     */
    protected function getAccessToken()
    {
        $request = new Request('https://id.twitch.tv/oauth2/token', Curl::class, [CURLOPT_POST => 1]);

        $api = $request
            ->withQueryParameter('grant_type', 'client_credentials')
            ->withQueryParameter('client_id', $this->config['client_id'])
            ->withQueryParameter('client_secret', $this->config['client_secret']);

        if ($json = $api->getJsonContent()) {
            return $json['access_token'];
        }

        return false;
    }
}
