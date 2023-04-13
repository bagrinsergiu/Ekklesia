<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class GroupLayoutPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_group_layout';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'show_category'                   => true,
            'parent_category'                 => '',
            'show_category_filter'            => true,
            'category_filter_parent'          => true,
            'category_filter_heading'         => 'Category',
            'show_category_filter_add1'       => false,
            'category_filter_parent_add1'     => '',
            'category_filter_heading_add1'    => 'Category',
            'show_category_filter_add2'       => false,
            'category_filter_parent_add2'     => '',
            'category_filter_heading_add2'    => 'Category',
            'show_category_filter_add3'       => true,
            'category_filter_parent_add3'     => '',
            'category_filter_heading_add3'    => 'Category',
            'show_group_filter'               => true,
            'group_filter_heading'            => 'Group',
            'show_search'                     => true,
            'search_placeholder'              => 'Search',
            'show_pagination'                 => true,
            'howmany'                         => 9,
            'column_count'                    => 3,
            'column_count_tablet'             => 2,
            'column_count_mobile'             => 1,
            'show_images'                     => true,
            'show_day'                        => true,
            'show_times'                      => true,
            'show_group'                      => true,
            'show_status'                     => true,
            'show_childcare'                  => true,
            'show_resourcelink'               => true,
            'show_preview'                    => true,
            'detail_page_button_text'         => false,
            'sticky_space'                    => 0,
            'detail_page'                     => false,
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $cms            = $this->monkCMS;
        $detail_url     = $settings['detail_page'] ? $this->replacer->replacePlaceholders(urldecode($settings['detail_page']), $context) : false;
        $page           = isset($_GET['mc-page']) ? $_GET['mc-page'] : 1;
        $baseURL        = strtok($_SERVER["REQUEST_URI"], '?') !== FALSE ? strtok($_SERVER["REQUEST_URI"], '?') : $_SERVER["REQUEST_URI"];
        $filterCountArr = [$show_category_filter, $show_category_filter_add1, $show_category_filter_add2, $show_category_filter_add3, $show_group_filter];
        $filterCount    = count(array_filter($filterCountArr));
        $group_filter   = isset($_GET['mc-group']) ? $_GET['mc-group'] : false;
        $categories     = $cms->get([
            'module'  => 'smallgroup',
            'display' => 'categories'
        ]);
        $categories_parent = $cms->get([
            'module'          => 'smallgroup',
            'display'         => 'categories',
            'parent_category' => $parent_category,
        ]);
        $groups = $cms->get([
            'module'  => 'smallgroup',
            'display' => 'list',
            'groupby' => 'group'
        ]);

        if (isset($_GET['mc-search'])) {
            //search is not allowing page so no pagination
            $content = [];
            $search_arr = $cms->get([
                'module'        => 'search',
                'display'       => 'results',
                'howmany'       => '100',
                'find_category' => $parent_category,
                'keywords'      => $_GET['mc-search'],
                'find_module'   => 'smallgroup',
                'hide_module'   => 'media',
            ]);

            $_search = isset($search_arr['show']) ? $search_arr['show'] : [];
            foreach ($_search as $search) {
                $item = $cms->get([
                    'module'      => 'smallgroup',
                    'display'     => 'detail',
                    'emailencode' => 'no',
                    'show'        => "__endtime format='g:ia'__",
                    'find'        => $search['slug'],
                ]);
                $content['show'][] = $item['show'];
            }

        } else {
            $content = $cms->get([
                'module'        => 'smallgroup',
                'display'       => 'list',
                'order'         => 'recent',
                'emailencode'   => 'no',
                'howmany'       => '100',
                'find_category' => $parent_category,
                'find_group'    => $group_filter,
                'show'          => "__endtime format='g:ia'__",
            ]);
            //filter categories separately since there can be more than 1 category filter
            if (!empty($_GET["mc-category"])) {
                $catArr = [];
                foreach ($categories["show"] as $key => $val) {
                    if ($val["slug"] == $_GET["mc-category"]) {
                        $catArr[] = $val["name"];
                    }
                }
                $content["show"] = self::searchArray($content["show"], $catArr);
            }
            if (!empty($_GET["mc-category-1"])) {
                $cat1Arr = [];
                foreach ($categories["show"] as $key => $val) {
                    if ($val["slug"] == $_GET["mc-category-1"]) {
                        $cat1Arr[] = $val["name"];
                    }
                }
                $content["show"] = self::searchArray($content["show"], $cat1Arr);
            }
            if (!empty($_GET["mc-category-2"])) {
                $cat2Arr = [];
                foreach ($categories["show"] as $key => $val) {
                    if ($val["slug"] == $_GET["mc-category-2"]) {
                        $cat2Arr[] = $val["name"];
                    }
                }
                $content["show"] = self::searchArray($content["show"], $cat2Arr);
            }
            if (!empty($_GET["mc-category-3"])) {
                $cat3Arr = [];
                foreach ($categories["show"] as $key => $val) {
                    if ($val["slug"] == $_GET["mc-category-3"]) {
                        $cat3Arr[] = $val["name"];
                    }
                }
                $content["show"] = self::searchArray($content["show"], $cat3Arr);
            }
        }

        ?>

        <div id="brz-groupLayout__filters" class="brz-groupLayout__filters">
            <form id="brz-groupLayout__filters--form" name="brz-groupLayout__filters--form" class="brz-groupLayout__filters--form" action="<?= $baseURL ?>" data-count="<?= $filterCount ?>">

                <?php if ($show_group_filter && !empty($groups['group_show'])): ?>
                    <div class="brz-groupLayout__filters--form-selectWrapper">
                    <select name="mc-group" class='sorter'>
                        <option value=""><?= $group_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        foreach ($groups['group_show'] as $group) {
                            echo "<option value=\"{$group['slug']}\"";
                            if (isset($_GET['group']) && $_GET['group'] == $group['slug']) {
                                echo " selected";
                            }
                            echo ">{$group['title']}</option>";
                        }
                        ?>
                    </select>
                    </div>
                <?php endif; ?>

                <?php
                if ($show_category_filter): ?>
                    <div class="brz-groupLayout__filters--form-selectWrapper">
                    <select name="mc-category" class='sorter'>
                        <option value=""><?= $category_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        //since this is the main category filter this will always show the
                        if ($category_filter_parent) {
                            foreach ($categories["level3"] as $category) {
                                if ($category["parentid"] != $category_filter_parent) {
                                    continue;
                                }
                                echo "<option value=\"{$category['slug']}\"";
                                if (isset($_GET['mc-category']) && $_GET['mc-category'] == $category['slug']) {
                                    echo " selected";
                                }
                                echo ">{$category['name']}</option>";
                            }
                        } else {
                            if ($parent_category != "" && !empty($categories_parent["level1"])) {
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
                    </div>
                <?php endif; ?>

                <?php
                if ($show_category_filter_add1 && $category_filter_parent_add1 != ""): ?>
                    <div class="brz-groupLayout__filters--form-selectWrapper">
                    <select name="mc-category-1" class='sorter'>
                        <option value=""><?= $category_filter_heading_add1 ?></option>
                        <option value="">All</option>
                        <?php
                        foreach ($categories["level3"] as $category) {
                            if ($category["parentid"] != $category_filter_parent_add1) {
                                continue;
                            }
                            echo "<option value=\"{$category['slug']}\"";
                            if (isset($_GET['mc-category-1']) && $_GET['mc-category-1'] == $category['slug']) {
                                echo " selected";
                            }
                            echo ">{$category['name']}</option>";
                        }
                        ?>
                    </select>
                    </div>
                <?php endif; ?>

                <?php
                if ($show_category_filter_add2 && $category_filter_parent_add2 != ""): ?>
                    <div class="brz-groupLayout__filters--form-selectWrapper">
                    <select name="mc-category-2" class='sorter'>
                        <option value=""><?= $category_filter_heading_add2 ?></option>
                        <option value="">All</option>
                        <?php
                        foreach ($categories["level3"] as $category) {
                            if ($category["parentid"] != $category_filter_parent_add2) {
                                continue;
                            }
                            echo "<option value=\"{$category['slug']}\"";
                            if (isset($_GET['mc-category-2']) && $_GET['mc-category-2'] == $category['slug']) {
                                echo " selected";
                            }
                            echo ">{$category['name']}</option>";
                        }
                        ?>
                    </select>
                    </div>
                <?php endif; ?>

                <?php
                if ($show_category_filter_add3 && $category_filter_parent_add3 != ""): ?>
                    <div class="brz-groupLayout__filters--form-selectWrapper">
                    <select name="mc-category-3" class='sorter'>
                        <option value=""><?= $category_filter_heading_add3 ?></option>
                        <option value="">All</option>
                        <?php
                        foreach ($categories["level3"] as $category) {
                            if ($category["parentid"] != $category_filter_parent_add3) {
                                continue;
                            }
                            echo "<option value=\"{$category['slug']}\"";
                            if (isset($_GET['mc-category-3']) && $_GET['mc-category-3'] == $category['slug']) {
                                echo " selected";
                            }
                            echo ">{$category['name']}</option>";
                        }
                        ?>
                    </select>
                    </div>
                <?php endif; ?>
            </form>

            <?php if ($show_search): ?>
                <form method="get" id="brz-groupLayout__filters--form-search" name="search" class="brz-groupLayout__filters--form-search" action="<?= $baseURL ?>" data-count="<?= $filterCount ?>">
                    <fieldset>
                        <input type="text" id="brz-groupLayout__filters--form-search_term" name="mc-search" value="" placeholder="<?= $search_placeholder ?>"/>
                        <button type="submit" id="brz-groupLayout__filters--form-search_submit"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="brz-icon-svg align-[initial]" data-type="fa" data-name="search"><path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path></svg></button>
                    </fieldset>
                </form>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['mc-search'])) {
        echo "<h4 class=\"ekklesia360_group_layout_results_heading\"><a href=\"{$baseURL}\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 352 512\" class=\"brz-icon-svg align-[initial]\" data-type=\"fa\" data-name=\"times\"><path d=\"M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z\"></path></svg></a> Search results for \"{$_GET['mc-search']}\"</h4>";
    }
        ?>

        <div id="brz-groupLayout__container" class="brz-groupLayout__container">

            <?php
            //setup pagination
            $_content  = isset($content["show"]) ? $content["show"] : [];
            $pagination = new CustomPagination($_content , (isset($page) ? $page : 1), $howmany);
            $pagination->setShowFirstAndLast(true);
            $resultsPagination = $pagination->getResults();
            //output
            if (count($resultsPagination) > 0) {
                ?>

                <div class="brz-groupLayout__content" data-columncount="<?php echo $column_count; ?>"
                     data-columncount-tablet="<?php echo $column_count_tablet; ?>"
                     data-columncount-mobile="<?php echo $column_count_mobile; ?>">
                    <?php
                    foreach ($resultsPagination as $key => $item) {
                        //remove 12am
                        if (strtolower($item['starttime']) == "12 am" || strtolower($item['starttime']) == "12:00am") {
                            $item['starttime'] = false;
                        }
                        if (strtolower($item['endtime']) == "12 am" || strtolower($item['endtime']) == "12:00am") {
                            $item['endtime'] = false;
                        }
                        //remove site group
                        $item['group'] = str_replace("Site Group, ", "", $item['group']);
                        $item['group'] = str_replace("Site Group", "", $item['group']);

                        echo "<div class=\"brz-groupLayout--item\">";
                        echo "<div class=\"brz-groupLayout--item__content\">";
                        if ($show_images && $item['imageurl']) {
                            if ($detail_url) echo "<a href=\"{$detail_url}?mc-slug={$item['slug']}\">";
                            echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                            if ($detail_url) echo "</a>";
                        }

                        echo "<h4 class=\"brz-groupLayout--item__content-heading\">";
                        if ($detail_url) echo "<a href=\"{$detail_url}?mc-slug={$item['slug']}\">";
                        echo "{$item['name']}";
                        if ($detail_url) echo "</a>";
                        echo "</h4>";

                        if ($show_day && $item['dayoftheweek']) {
                            echo "<h6 class=\"brz-groupLayout--item__content-meta\">Meeting Day: {$item['dayoftheweek']}</h6>";
                        }
                        if ($show_times && ($item['starttime'] || $item['endtime'])) {
                            echo "<h6 class=\"brz-groupLayout--item__content-meta\">Meeting Time: ";
                            if ($item['starttime']) echo "{$item['starttime']}";
                            if ($item['endtime']) echo " - {$item['endtime']}";
                            echo "</h6>";
                        }
                        if ($show_category && $item['category']) {
                            echo "<h6 class=\"brz-groupLayout--item__content-meta\">Category: {$item['category']}</h6>";
                        }
                        if ($show_group && $item['group']) {
                            echo "<h6 class=\"brz-groupLayout--item__content-meta\">Group: {$item['group']}</h6>";
                        }
                        if ($show_status && $item['groupstatus']) {
                            echo "<h6 class=\"brz-groupLayout--item__content-meta\">Status: {$item['groupstatus']}</h6>";
                        }
                        if ($show_childcare) {
                            $childcare = "No";
                            if ($item['childcareprovided']) {
                                $childcare = "Yes";
                            }
                            echo "<h6 class=\"brz-groupLayout--item__content-meta\">Childcare Provided: {$childcare}</h6>";
                        }
                        if ($show_resourcelink && $item['resourcelink']) {
                            $resource_target = "";
                            if ($item['iflinknewwindow']) {
                                $resource_target = " target=\"_blank\"";
                            }
                            echo "<h6 class=\"brz-groupLayout--item__content-meta\">Resource: <a href=\"{$item['resourcelink']}\" {$resource_target}>{$item['resourcelink']}</a></h6>";
                        }
                        if ($show_preview && $item['description']) {
                            $item['description'] = substr($item['description'], 0, 110) . "<span class=\"brz-groupLayout--item__content-preview--more\">...</span>";
                            $item['description'] = str_replace("<p>","<p class=\"brz-groupLayout--item__content-preview\">",$item['description']);

                            echo $item['description'];
                        }
                        if ($detail_url && $detail_page_button_text) {
                            echo "<p class=\"brz-groupLayout--item__content-detailButton\"><a href=\"{$detail_url}?mc-slug={$item['slug']}\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">{$detail_page_button_text}</span></a></p>";
                        }
                        echo "</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
                <?php
                if ($show_pagination) {

                    $paginationOutput = '<p id="brz-groupLayout__pagination" class="brz-groupLayout__pagination">' . $pagination->getLinks($_GET, 'mc-page') . '</p>';

                    //if complexity grows consider http_build_query
                    if (isset($_GET['mc-search'])) {
                        $paginationOutput = str_replace('?', "?mc-search={$_GET['mc-search']}&", $paginationOutput);
                    }

                    //add group
                    if (isset($_GET['mc-group'])) {
                        $paginationOutput = str_replace('?', "?mc-group={$_GET['mc-group']}&", $paginationOutput);
                    }
                    //add category
                    if (isset($_GET['mc-category'])) {
                        $paginationOutput = str_replace('?', "?mc-category={$_GET['mc-category']}&", $paginationOutput);
                    }
                    //add1 category
                    if (isset($_GET['mc-category-1'])) {
                        $paginationOutput = str_replace('?', "?mc-category-1={$_GET['mc-category-1']}&", $paginationOutput);
                    }
                    //add2 category
                    if (isset($_GET['mc-category-2'])) {
                        $paginationOutput = str_replace('?', "?mc-category-2={$_GET['mc-category-2']}&", $paginationOutput);
                    }
                    //add3 category
                    if (isset($_GET['mc-category-3'])) {
                        $paginationOutput = str_replace('?', "?mc-category-3={$_GET['mc-category-3']}&", $paginationOutput);
                    }
                    echo $paginationOutput;
                }
            } //no output
            else {
                ?>

                <p>There are no groups available.</p>

                <?php
            }
            ?>
        </div>
        <?php
    }

    /**
     * only searches for the expanded category filtering options.  all other options handled within api
     *
     * @param $groups
     * @param $categories
     * @return array
     */
    private function searchArray($groups = [], $categories = [])
    {
        $results = [];
        foreach ($groups as $group) {
            $pieces  = explode(", ", $group["category"]);
            $matches = array_intersect($pieces, $categories);

            if (count($matches) > 0) {
                $results[] = $group;
            }
        }

        return $results;
    }
}