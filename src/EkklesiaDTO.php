<?php

namespace BrizyEkklesia;

class EkklesiaDTO
{
    private $site_id;

    private $secret;

    public function __construct($siteId, $secret)
    {
        $this->site_id = $siteId;
        $this->secret  = $secret;
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
}