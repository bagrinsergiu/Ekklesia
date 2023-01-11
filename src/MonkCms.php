<?php

namespace BrizyEkklesia;

use Exception;
use Monk\Cms;

class MonkCms
{
    /**
     * @var string
     */
    private $siteId;

    /**
     * @var string
     */
    private $siteSecret;

    public function __construct(EkklesiaDTO $data)
    {
        $this->siteId     = $data->getSiteId();
        $this->siteSecret = $data->getSecret();
    }

    /**
     * @throws Exception
     */
    public function get($config = [])
    {
        $cms = new Cms([
            'siteId'     => $this->siteId,
            'siteSecret' => $this->siteSecret
        ]);

        return $cms->get($config);
    }
}