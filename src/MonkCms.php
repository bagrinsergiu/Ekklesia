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

    public function __construct($siteId, $siteSecret)
    {
        $this->siteId     = $siteId;
        $this->siteSecret = $siteSecret;
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