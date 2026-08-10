<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;

interface PrefetchableInterface
{
    /**
     * MonkCMS queries this placeholder will run while rendering, derivable
     * before render time. Queries whose params depend on another API response
     * must not be returned — they will run sequentially on cache miss.
     *
     * @return array[] List of query params arrays, same format as MonkCms::get().
     */
    public function getPrefetchQueries(ContentPlaceholder $placeholder): array;

    public function getMonkCms(): \BrizyEkklesia\MonkCms;
}
