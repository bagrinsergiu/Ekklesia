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

	/**
     * @var string
     */
    private $accountId;

	/**
     * @var string
     */
    private $visitorId;

	/**
     * @var string
     */
    private $themeName;

    public function __construct($siteId, $secret, $accountId, $visitorId, $themeName, $url = '')
    {
	    $this->site_id   = $siteId;
	    $this->secret    = $secret;
	    $this->apiUrl    = $url;
	    $this->accountId = $accountId;
	    $this->visitorId = $visitorId;
	    $this->themeName = $themeName;
    }

    /**
     * @param $json
     *
     * @return EkklesiaConfig
     */
    static public function factoryFromJson($json)
    {
        $data   = $json ? json_decode($json, true) : [];
        $siteId = isset($data['site_id']) ? $data['site_id'] : null;
        $secret = isset($data['secret']) ? $data['secret'] : null;
	    $accountId = isset($data['MBAccountID']) ? $data['MBAccountID'] : '';
	    $visitorId = isset($data['MBVisitorID']) ? $data['MBVisitorID'] : '';
	    $themeName = isset($data['MBThemeName']) ? $data['MBThemeName'] : '';

        return new self($siteId, $secret, $accountId, $visitorId, $themeName);
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

	/**
	 * @return string
	 */
	public function getAccountId()
	{
		return $this->accountId;
	}

	/**
	 * @param string $accountId
	 */
	public function setAccountId($accountId)
	{
		$this->accountId = $accountId;
	}

	/**
	 * @return string
	 */
	public function getVisitorId()
	{
		return $this->visitorId;
	}

	/**
	 * @param string $visitorId
	 */
	public function setVisitorId($visitorId)
	{
		$this->visitorId = $visitorId;
	}

	/**
	 * @return string
	 */
	public function getThemeName()
	{
		return $this->themeName;
	}

	/**
	 * @param string $themeName
	 */
	public function setThemeName($themeName)
	{
		$this->themeName = $themeName;
	}
}