<?php

namespace BrizyEkklesia;

class EkklesiaConfig
{
    /**
     * @var string
     */
    private $site_id;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $url;

    public function __construct($siteId, $secret, $url = '')
    {
        $this->site_id = $siteId;
        $this->secret  = $secret;
        $this->apiUrl  = $url;
    }

    /**
     * @param $json
     * @return EkklesiaDTO
     */
    static public function factoryFromJson($json)
    {
        $data   = $json ? json_decode($json, true) : [];
        $siteId = isset($data['site_id']) ? $data['site_id'] : null;
        $secret = isset($data['secret']) ? $data['secret'] : null;

        return new self($siteId, $secret);
    }

    public function toArray()
    {
        return [
            'siteId'     => $this->getSiteId(),
            'siteSecret' => $this->getSecret(),
            'url'        => $this->getUrl()
        ];
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return !empty($this->site_id) && !empty($this->secret);
    }

    /**
     * @return mixed
     */
    public function getSiteId()
    {
        return $this->site_id;
    }

    /**
     * @param mixed $site_id
     */
    public function setSiteId($site_id)
    {
        $this->site_id = $site_id;
    }

    /**
     * @return mixed
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param mixed $secret
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @param string $url
     */
    public function setUrl($url): void
    {
        $this->apiUrl = $url;
    }
}