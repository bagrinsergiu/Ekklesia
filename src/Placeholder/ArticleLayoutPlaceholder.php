<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class ArticleLayoutPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_article_layout';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'parent_category'              => 'articles',
            'show_category_filter'         => true,
            'category_filter_parent'       => 'childrens-ministry',
            'category_filter_heading'      => 'Category',
            'show_category_filter_add1'    => false,
            'category_filter_parent_add1'  => '',
            'category_filter_heading_add1' => 'Category',
            'show_category_filter_add2'    => false,
            'category_filter_parent_add2'  => '',
            'category_filter_heading_add2' => 'Category',
            'show_category_filter_add3'    => false,
            'category_filter_parent_add3'  => '',
            'category_filter_heading_add3' => 'Category',
            'show_group_filter'            => true,
            'group_filter_heading'         => 'Group',
            'show_series_filter'           => true,
            'series_filter_heading'        => 'Series',
            'show_author_filter'           => true,
            'author_filter_heading'        => 'Author',
            'show_search'                  => true,
            'search_placeholder'           => 'Search',
            'show_pagination'              => true,
            'howmany'                      => 3,
            'show_images'                  => true,
            'show_video'                   => false,
            'show_audio'                   => false,
            'show_media_links'             => false,
            'show_title'                   => true,
            'show_date'                    => false,
            'show_category'                => false,
            'show_group'                   => false,
            'show_series'                  => false,
            'show_author'                  => false,
            'show_meta_headings'           => false,
            'show_preview'                 => false,
            'detail_page_button_text'      => '',
            'detail_page'                  => '',
        ];

        $settings = array_merge($options, $placeholder->getAttributes());
        $cms      = $this->monkCMS;

        extract($settings);

        $baseURL     = strtok($_SERVER["REQUEST_URI"], '?') !== FALSE ? strtok($_SERVER["REQUEST_URI"], '?') : $_SERVER["REQUEST_URI"];
        $filterCount = count(array_filter([$show_category_filter, $show_category_filter_add1, $show_category_filter_add2, $show_category_filter_add3, $show_group_filter, $show_series_filter, $show_author_filter]));
        $detail_url  = $detail_page ? $this->replacer->replacePlaceholders(urldecode($detail_page), $context) : '';
        $page        = isset($_GET['mc-page']) ? $_GET['mc-page'] : 1;

        $categories        = $cms->get(['module' => 'article', 'display' => 'categories']);
        $categories_parent = $cms->get(['module' => 'article', 'display' => 'categories', 'parent_category' => $parent_category,]);
        $groups            = $cms->get(['module' => 'article', 'display' => 'list', 'groupby' => 'group', 'find_parent_category' => $parent_category,]);
        $series            = $cms->get(['module' => 'article', 'display' => 'list', 'groupby' => 'series', 'find_parent_category' => $parent_category,]);
        $authors           = $cms->get(['module' => 'article', 'display' => 'list', 'groupby' => 'author', 'find_parent_category' => $parent_category,]);

        if (isset($_GET['mc-search_term'])) {
            $content    = [];
            $search_arr = $cms->get([
                'module'        => 'search',
                'display'       => 'results',
                'howmany'       => '100',
                'find_category' => $parent_category,
                'keywords'      => $_GET['mc-search_term'],
                'find_module'   => 'article',
                'hide_module'   => 'media',
            ]);

            foreach ($search_arr['show'] as $search) {
                $item = $cms->get([
                    'module'      => 'article',
                    'display'     => 'detail',
                    'emailencode' => 'no',
                    'show'        => "__videoplayer fullscreen='true'__",
                    'show'        => "__audioplayer__",
                    'find'        => $search['slug'],
                ]);
                $content['show'][] = $item['show'];
            }
        } else {
            $content = $cms->get([
                'module'               => 'article',
                'display'              => 'list',
                'order'                => 'recent',
                'emailencode'          => 'no',
                'howmany'              => '100',
                'find_parent_category' => $parent_category,
                'find_group'           => isset($_GET['mc-group']) ? $_GET['mc-group'] : '',
                'find_series'          => isset($_GET['mc-series']) ? $_GET['mc-series'] : '',
                'find_author'          => isset($_GET['mc-author']) ? $_GET['mc-author'] : '',
                'show'                 => "__videoplayer fullscreen='true'__",
                'show'                 => "__audioplayer__",
            ]);
            //filter categories separately since there can be more than 1 category filter
            if (!empty($_GET['mc-category'])) {
                $content['show'] = self::searchArray($content['show'], $_GET['mc-category']);
            }
            if (!empty($_GET['mc-category_add1'])) {
                $content['show'] = self::searchArray($content['show'], $_GET['mc-category_add1']);
            }
            if (!empty($_GET['mc-category_add2'])) {
                $content['show'] = self::searchArray($content['show'], $_GET['mc-category_add2']);
            }
            if (!empty($_GET['mc-category_add3'])) {
                $content['show'] = self::searchArray($content['show'], $_GET['mc-category_add3']);
            }
        }

        ?>

        <div id="brz-articleLayout__filters" class="brz-articleLayout__filters">
            <form id="brz-articleLayout__form" name="brz-articleLayout__form" action="<?= $baseURL ?>" data-count="<?= $filterCount ?>">
                <?php if($show_group_filter && !empty($groups['group_show'])): ?>
                    <select name="mc-group" class="sorter">
                        <option value=""><?= $group_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        foreach ($groups['group_show'] as $group) {
                            echo "<option value=\"{$group['slug']}\"";
                            if (isset($_GET['mc-group']) && $_GET['mc-group'] == $group['slug']) {
                                echo " selected";
                            }
                            echo ">{$group['title']}</option>";
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <?php
                if($show_category_filter): ?>
                    <select name="mc-category" class="brz-articleLayout__sorter">
                        <option value=""><?= $category_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        //since this is the main category filter this will always show the
                        if ($category_filter_parent && !empty($categories['level3'])) {
                            foreach ($categories['level3'] as $category) {
                                if ($category['slug'] != $category_filter_parent) {
                                    continue;
                                }
                                echo "<option value=\"{$category['slug']}\"";
                                if (isset($_GET['mc-category']) && $_GET['mc-category'] == $category['slug']) {
                                    echo " selected";
                                }
                                echo ">{$category['name']}</option>";
                            }
                        } else {
                            if ($parent_category != "") {
                                foreach ($categories_parent["level1"] as $category) {
                                    echo "<option value=\"{$category['slug']}\"";
                                    if (isset($_GET['mc-category']) && $_GET['mc-category'] == $category['slug']) {
                                        echo " selected";
                                    }
                                    echo ">{$category['name']}</option>";
                                }
                            } else {
                                foreach ($categories["show"] as $category) {
                                    echo "<option value=\"{$category['slug']}\"";
                                    if (isset($_GET['mc-category']) && $_GET['mc-category'] == $category['slug']) {
                                        echo " selected";
                                    }
                                    echo ">{$category['name']}</option>";
                                }
                            }
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <?php
                if($show_category_filter_add1 && $category_filter_parent_add1): ?>
                    <select name="mc-category_add1" class="brz-articleLayout__sorter--filter">
                        <option value=""><?= $category_filter_heading_add1 ?></option>
                        <option value="">All</option>
                        <?php
                        foreach ($categories["level3"] as $category) {
                            if ($category["slug"] != $category_filter_parent_add1) {
                                continue;
                            }
                            echo "<option value=\"{$category['slug']}\"";
                            if (isset($_GET['mc-category_add1']) && $_GET['mc-category_add1'] == $category['slug']) {
                                echo " selected";
                            }
                            echo ">{$category['name']}</option>";
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <?php
                if($show_category_filter_add2 && $category_filter_parent_add2 != ""): ?>
                    <select name="mc-category_add2" class="brz-articleLayout__sorter--filter">
                        <option value=""><?= $category_filter_heading_add2 ?></option>
                        <option value="">All</option>
                        <?php
                        foreach ($categories["level3"] as $category) {
                            if ($category["slug"] != $category_filter_parent_add2) {
                                continue;
                            }
                            echo "<option value=\"{$category['slug']}\"";
                            if (isset($_GET['mc-category_add2']) && $_GET['mc-category_add2'] == $category['slug']) {
                                echo " selected";
                            }
                            echo ">{$category['name']}</option>";
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <?php
                if($show_category_filter_add3 && $category_filter_parent_add3 != ""): ?>
                    <select name="mc-category_add3" class="brz-articleLayout__sorter--filter">
                        <option value=""><?= $category_filter_heading_add3 ?></option>
                        <option value="">All</option>
                        <?php
                        foreach ($categories["level3"] as $category) {
                            if ($category["slug"] != $category_filter_parent_add3) {
                                continue;
                            }
                            echo "<option value=\"{$category['slug']}\"";
                            if (isset($_GET['mc-category_add3']) && $_GET['mc-category_add3'] == $category['slug']) {
                                echo " selected";
                            }
                            echo ">{$category['name']}</option>";
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <?php if($show_series_filter && count($series['group_show']) > 0): ?>
                    <select name="mc-series" class="brz-articleLayout__sorter--series">
                        <option value=""><?= $series_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        foreach ($series['group_show'] as $serie) {
                            echo "<option value=\"{$serie['slug']}\"";
                            if (isset($_GET['mc-series']) && $_GET['mc-series'] == $serie['slug']) {
                                echo " selected";
                            }
                            echo ">{$serie['title']}</option>";
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <?php if($show_author_filter && count($authors['group_show']) > 0): ?>
                    <select name="mc-author" class="brz-articleLayout__sorter--author">
                        <option value=""><?= $author_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        foreach ($authors['group_show'] as $val) {
                            echo "<option value=\"{$val['slug']}\"";
                            if (isset($_GET['mc-author']) && $_GET['mc-author'] == $val['slug']) {
                                echo " selected";
                            }
                            echo ">{$val['title']}</option>";
                        }
                        ?>
                    </select>
                <?php endif; ?>
            </form>

            <?php if ($show_search): ?>
                <form method="get" id="brz-articleLayout__search" name="mc-search" action="<?= $baseURL ?>" data-count="<?= $filterCount ?>">
                    <fieldset>
                        <input type="text" id="brz-articleLayout__search_term" name="mc-search_term" value="" placeholder="<?= $search_placeholder ?>"/>
                        <button type="submit" name="submit" id="brz-articleLayout__search_submit" class="brz-articleLayout__search_submit" value="">
                            <i class="brz-icon fas fa-search"></i>
                        </button>
                    </fieldset>
                </form>
            <?php endif; ?>
        </div>

        <?php
        if (isset($_GET['mc-search_term'])) {
            echo "<h4 class=\"brz-articleLayout__results_heading\"><a href=\"{$baseURL}\"><i class=\"brz-icon fas fa-times\"></i></a> Search results for \"{$_GET['mc-search_term']}\"</h4>";
        }
        ?>
        <div id="brz-articleLayout__wrap" class="brz-articleLayout__wrap">

            <?php

            $pagination = new CustomPagination($content["show"] ?: [] , $page, $howmany);
            $pagination->setShowFirstAndLast(true);
            $resultsPagination = $pagination->getResults();

            if (count($resultsPagination) > 0) { ?>
                <div class="brz-articleLayout">
                    <?php
                    foreach ($resultsPagination as $key => $item) {
                        echo "<article>";
                        echo "<div class=\"brz-articleLayout__info\">";

                        if ($show_images && $item['imageurl'] && !$show_video) {
                            if ($detail_url) {
                                echo "<a href=\"{$detail_url}?ekklesia360_article_slug={$item['slug']}\">";
                            }
                            echo "<div class=\"brz-articleLayout__image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                            if ($detail_url) {
                                echo "</a>";
                            }
                        }
                        if ($show_video) {
                            if ($item['videoembed']) {
                                echo "<div class=\"brz-articleLayout__media_responsive--video\">{$item['videoembed']}</div>";
                            } elseif ($item['videourl']) {
                                $videoext = pathinfo($item['videourl'], PATHINFO_EXTENSION);
                                echo "<div class=\"brz-articleLayout__media_responsive\">";
                                echo "<video src=\"{$item['videourl']}\" controls preload=\"none\" width=\"1024\" height=\"576\" poster=\"{$item['imageurl']}\" type=\"video/{$videoext}\"><p>The Video could not be played. Please <a href=\"{$item['videourl']}\" target=\"_blank\">download it here</a>.</p></video>";
                                echo "</div>";
                            } elseif ($show_images && $item['imageurl']) {
                                echo "<div class=\"brz-articleLayout__image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                            }
                        }
                        if ($show_audio && $item['audiourl']) {
                            echo "<div class=\"brz-articleLayout__media_audio\">";
                            echo "<audio src=\"{$item['audiourl']}\" controls preload=\"none\"></audio>";
                            echo "</div>";
                        }

                        if ($show_title) {
                            echo "<h4 class=\"brz-articleLayout__heading\">";
                            if ($detail_url) {
                                echo "<a href=\"{$detail_url}?ekklesia360_article_slug={$item['slug']}\">";
                            }
                            echo "{$item['title']}";
                            if ($detail_url) {
                                echo "</a>";
                            }
                            echo "</h4>";
                        }

                        if ($show_date && $item['date']) {
                            echo "<h6 class=\"brz-articleLayout__meta\">";
                            if ($show_meta_headings) {
                                echo "Date: ";
                            }
                            echo "{$item['date']}";
                            echo "</h6>";
                        }
                        if ($show_category && $item['category']) {
                            echo "<h6 class=\"brz-articleLayout__meta\">";
                            if ($show_meta_headings) {
                                echo "Category: ";
                            }
                            echo "{$item['category']}";
                            echo "</h6>";
                        }
                        if ($show_group && $item['group']) {
                            echo "<h6 class=\"brz-articleLayout__meta\">";
                            if ($show_meta_headings) {
                                echo "Group: ";
                            }
                            echo "{$item['group']}";
                            echo "</h6>";
                        }
                        if ($show_series && $item['series']) {
                            echo "<h6 class=\"brz-articleLayout__meta\">";
                            if ($show_meta_headings) {
                                echo "Series: ";
                            }
                            echo "{$item['series']}";
                            echo "</h6>";
                        }
                        if ($show_author && $item['author']) {
                            echo "<h6 class=\"brz-articleLayout__meta\">";
                            if ($show_meta_headings) {
                                echo "Author: ";
                            }
                            echo "{$item['author']}";
                            echo "</h6>";
                        }
                        if ($show_media_links) {
                            echo "<ul class=\"brz-articleLayout__media\">";
                            if ($item['videoplayer']) {
                                $item['videoplayer'] = preg_replace(
                                    '/<a(.+?)>.+?<\/a>/i',
                                    "<a$1><i class=\"brz-icon fas fa-desktop\"></i></a>",
                                    $item['videoplayer']
                                );
                                echo "<li class=\"brz-articleLayout__media_videoplayer\">{$item['videoplayer']}</li>";
                            }
                            if ($item['audioplayer']) {
                                $item['audioplayer'] = preg_replace(
                                    '/<a(.+?)>.+?<\/a>/i',
                                    "<a$1><i class=\"brz-icon fas fa-volume-up\"></i></a>",
                                    $item['audioplayer']
                                );
                                echo "<li class=\"brz-articleLayout__media_audioplayer\">{$item['audioplayer']}</li>";
                            }
                            if ($item['notes']) {
                                echo "<li class=\"brz-articleLayout__media_notes\"><a href=\"{$item['notes']}\" target=\"_blank\"><i class=\"brz-icon fas fa-file-alt\"></i></a></li>";
                            }
                            echo "</ul>";
                        }
                        if ($show_preview && $item['preview']) {
                            $item['preview'] = substr($item['preview'], 0, 110)." ...";
                            echo "<p class=\"brz-articleLayout__preview\">{$item['preview']}</p>";
                        }

                        if ($detail_url && $detail_page_button_text) {
                            echo "<p class=\"brz-articleLayout__detail_button\"><a href=\"{$detail_url}?ekklesia360_article_slug={$item['slug']}\" class=\"brz-articleLayout__button-link\"><span class=\"brz-articleLayout__button-text\">{$detail_page_button_text}</span></a></p>";
                        }

                        echo "</div>";
                        echo "</article>";
                    }
                    ?>
                </div>
                <?php
                if ($show_pagination) {
                    $paginationOutput = '<p id="brz-articleLayout__pagination" class="brz-articleLayout__pagination">'.$pagination->getLinks($_GET, 'mc-page').'</p>';

                    if (!empty($_GET['mc-search'])) {
                        $paginationOutput = str_replace('?', "?mc-search={$_GET['mc-search']}&", $paginationOutput);
                    }

                    if (!empty($_GET['mc-search_term'])) {
                        $paginationOutput = str_replace('?', "?mc-search_term={$_GET['mc-search_term']}&", $paginationOutput);
                    }

                    if (!empty($_GET['mc-group'])) {
                        $paginationOutput = str_replace('?', "?mc-group={$_GET['mc-group']}&", $paginationOutput);
                    }

                    if (!empty($_GET['mc-category'])) {
                        $paginationOutput = str_replace('?', "?mc-category={$_GET['mc-category']}&", $paginationOutput);
                    }

                    if (!empty($_GET['mc-category_add1'])) {
                        $paginationOutput = str_replace('?', "?mc-category_add1={$_GET['mc-category_add1']}&", $paginationOutput);
                    }

                    if (!empty($_GET['mc-category_add2'])) {
                        $paginationOutput = str_replace('?', "?mc-category_add2={$_GET['mc-category_add2']}&", $paginationOutput);
                    }

                    if (!empty($_GET['mc-category_add3'])) {
                        $paginationOutput = str_replace('?', "?mc-category_add3={$_GET['mc-category_add3']}&", $paginationOutput);
                    }

                    if (!empty($_GET['mc-series'])) {
                        $paginationOutput = str_replace('?', "?mc-series={$_GET['mc-series']}&", $paginationOutput);
                    }

                    if (!empty($_GET['mc-author'])) {
                        $paginationOutput = str_replace('?', "?mc-author={$_GET['mc-author']}&", $paginationOutput);
                    }

                    echo $paginationOutput;
                }
            } else { ?>
                <p>There are no items available.</p>
            <?php } ?>
        </div>
        <?php
    }

    /**
     * only searches for the expanded category filtering options.  all other options handled within api
     * also accounts for how the api reveals categories and category slugs with parent category.
     *
     * @param $items
     * @param $category
     * @return array
     */
    public function searchArray($items = [], $category = '')
    {
        $results = [];
        foreach ($items as $item) {
            $pieces        = explode(", ", $item["category"]);
            $categoriesArr = [];
            $count         = 1;

            foreach ($pieces as $piece) {
                $catcall         = "category{$count}slug";
                $categoriesArr[] = $item[$catcall];
                $count++;
            }

            if (in_array($category, $categoriesArr)) {
                $results[] = $item;
            }
        }

        return $results;
    }
}
