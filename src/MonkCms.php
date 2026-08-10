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

    /**
     * Request-scoped response cache, keyed by canonical query.
     *
     * @var array
     */
    private $cache = [];

    /**
     * @var Cms|null
     */
    private $cms;

    public function __construct(EkklesiaConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @throws Exception
     */
    public function get($args = [])
    {
        $key = $this->cacheKey($args);

        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        return $this->cache[$key] = $this->getCms()->get($args);
    }

    /**
     * Fetch multiple queries in parallel and warm the response cache.
     *
     * Queries already cached are skipped. Failed queries are left uncached,
     * so a later `get()` retries them sequentially.
     *
     * @param array[] $queries List of query params arrays, same format as `get()`.
     */
    public function prefetch(array $queries)
    {
        $pending = [];

        foreach ($queries as $args) {
            if (!is_array($args) || !$args) {
                continue;
            }

            $key = $this->cacheKey($args);

            if (!array_key_exists($key, $this->cache) && !isset($pending[$key])) {
                $pending[$key] = $args;
            }
        }

        if (!$pending) {
            return;
        }

        try {
            $results = $this->getCms()->getMultiple($pending);
        } catch (\Exception $e) {
            return;
        }

        foreach ($results as $key => $result) {
            $this->cache[$key] = $result;
        }
    }

    private function getCms(): Cms
    {
        if ($this->cms !== null) {
            return $this->cms;
        }

        $config = $this->config->toArray();

        if (isset($config['url']) && empty($config['url'])) {
            unset($config['url']);
        }

        return $this->cms = new Cms($config, ['timeout' => 40]);
    }

    private function cacheKey($args): string
    {
        $args = array_filter((array)$args);
        ksort($args);

        return md5(serialize($args));
    }
}
