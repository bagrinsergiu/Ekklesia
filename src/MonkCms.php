<?php

namespace BrizyEkklesia;

use Exception;
use Monk\Cms;

class MonkCms
{
    /**
     * @var EkklesiaDTO
     */
    private $config;

    /**
     * @var string
     */
    private $siteId;

    /**
     * @var string
     */
    private $siteSecret;

    /**
     * @var string
     */
    private $apiUrl;

    public function __construct(EkklesiaConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @throws Exception
     */
    public function get($args = [])
    {
        $config = $this->config->toArray();

        if (isset($config['url']) && empty($config['url'])) {
            unset($config['url']);
        }

        $cms = new Cms($this->config->toArray());

        return $cms->get($args);
    }
}