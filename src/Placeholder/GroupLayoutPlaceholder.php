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
            'column_countcolumn_count_tablet' => 2,
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
        $detail_url     = $settings['detail_page'] ? home_url($settings['detail_page']) : false;
        $page           = isset($_GET['ekklesia360_group_layout_page']) ? $_GET['ekklesia360_group_list_page'] : 1;
        $baseURL        = strtok($_SERVER["REQUEST_URI"], '?') !== FALSE ? strtok($_SERVER["REQUEST_URI"], '?') : $_SERVER["REQUEST_URI"];
        $filterCountArr = [$show_category_filter, $show_category_filter_add1, $show_category_filter_add2, $show_category_filter_add3, $show_group_filter];
        $filterCount    = count(array_filter($filterCountArr));
        $group_filter   = isset($_GET['group']) ? $_GET['category'] : false;
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

        if (isset($_GET['search_term'])) {
            //search is not allowing page so no pagination
            $content = [];
            $search_arr = $cms->get([
                'module'        => 'search',
                'display'       => 'results',
                'howmany'       => '100',
                'find_category' => $parent_category,
                'keywords'      => $_GET['search_term'],
                'find_module'   => 'smallgroup',
                'hide_module'   => 'media',
            ]);

            foreach ($search_arr['show'] as $search) {
                $item = $cms->get([
                    'module'      => 'smallgroup',
                    'display'     => 'detail',
                    'emailencode' => 'no',
                    'show'        => "__starttime format='g:ia'__",
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
                'show'          => "__starttime format='g:ia'__",
                'show'          => "__endtime format='g:ia'__",
            ]);
            //filter categories separately since there can be more than 1 category filter
            if (isset($_GET["category"])) {
                foreach ($categories["show"] as $key => $val) {
                    if ($val["slug"] == $_GET["category"]) {
                        $catArr[] = $val["name"];
                    }
                }
                $content["show"] = self::searchArray($content["show"], $catArr);
            }
            if (isset($_GET["category_add1"])) {
                foreach ($categories["show"] as $key => $val) {
                    if ($val["slug"] == $_GET["category_add1"]) {
                        $cat1Arr[] = $val["name"];
                    }
                }
                $content["show"] = self::searchArray($content["show"], $cat1Arr);
            }
            if (isset($_GET["category_add2"])) {
                foreach ($categories["show"] as $key => $val) {
                    if ($val["slug"] == $_GET["category_add2"]) {
                        $cat2Arr[] = $val["name"];
                    }
                }
                $content["show"] = self::searchArray($content["show"], $cat2Arr);
            }
            if (isset($_GET["category_add3"])) {
                foreach ($categories["show"] as $key => $val) {
                    if ($val["slug"] == $_GET["category_add3"]) {
                        $cat3Arr[] = $val["name"];
                    }
                }
                $content["show"] = self::searchArray($content["show"], $cat3Arr);
            }
        }
        ?>

        <div id="ekklesia360_group_layout_filters" class="ekklesia360_group_layout_filters">
            <form id="ekklesia360_group_layout_form" name="ekklesia360_group_layout_form" action="<?= $baseURL ?>"
                  data-count="<?= $filterCount ?>">

                <?php if ($show_group_filter && !empty($groups['group_show'])): ?>
                    <select name="group" class='sorter' onchange='filterEkklesia360Groups()'>
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
                <?php endif; ?>

                <?php
                if ($show_category_filter): ?>
                    <select name="category" class='sorter' onchange='filterEkklesia360Groups()'>
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
                                if (isset($_GET['category']) && $_GET['category'] == $category['slug']) {
                                    echo " selected";
                                }
                                echo ">{$category['name']}</option>";
                            }
                        } else {
                            if ($parent_category != "") {
                                foreach ($categories_parent["level1"] as $category) {
                                    echo "<option value=\"{$category['slug']}\"";
                                    if (isset($_GET['category']) && $_GET['category'] == $category['slug']) {
                                        echo " selected";
                                    }
                                    echo ">{$category['name']}</option>";
                                }
                            } else {
                                foreach ($categories["show"] as $category) {
                                    echo "<option value=\"{$category['slug']}\"";
                                    if (isset($_GET['category']) && $_GET['category'] == $category['slug']) {
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
                if ($show_category_filter_add1 && $category_filter_parent_add1 != ""): ?>
                    <select name="category_add1" class='sorter' onchange='filterEkklesia360Groups()'>
                        <option value=""><?= $category_filter_heading_add1 ?></option>
                        <option value="">All</option>
                        <?php
                        foreach ($categories["level3"] as $category) {
                            if ($category["parentid"] != $category_filter_parent_add1) {
                                continue;
                            }
                            echo "<option value=\"{$category['slug']}\"";
                            if (isset($_GET['category_add1']) && $_GET['category_add1'] == $category['slug']) {
                                echo " selected";
                            }
                            echo ">{$category['name']}</option>";
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <?php
                if ($show_category_filter_add2 && $category_filter_parent_add2 != ""): ?>
                    <select name="category_add2" class='sorter' onchange='filterEkklesia360Groups()'>
                        <option value=""><?= $category_filter_heading_add2 ?></option>
                        <option value="">All</option>
                        <?php
                        foreach ($categories["level3"] as $category) {
                            if ($category["parentid"] != $category_filter_parent_add2) {
                                continue;
                            }
                            echo "<option value=\"{$category['slug']}\"";
                            if (isset($_GET['category_add2']) && $_GET['category_add2'] == $category['slug']) {
                                echo " selected";
                            }
                            echo ">{$category['name']}</option>";
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <?php
                if ($show_category_filter_add3 && $category_filter_parent_add3 != ""): ?>
                    <select name="category_add3" class='sorter' onchange='filterEkklesia360Groups()'>
                        <option value=""><?= $category_filter_heading_add3 ?></option>
                        <option value="">All</option>
                        <?php
                        foreach ($categories["level3"] as $category) {
                            if ($category["parentid"] != $category_filter_parent_add3) {
                                continue;
                            }
                            echo "<option value=\"{$category['slug']}\"";
                            if (isset($_GET['category_add3']) && $_GET['category_add3'] == $category['slug']) {
                                echo " selected";
                            }
                            echo ">{$category['name']}</option>";
                        }
                        ?>
                    </select>
                <?php endif; ?>
            </form>

            <?php if ($show_search): ?>
                <form method="get" id="ekklesia360_group_layout_search" name="search" action="<?= $baseURL ?>"
                      data-count="<?= $filterCount ?>">
                    <fieldset>
                        <input type="text" id="ekklesia360_group_layout_search_term" name="search_term" value=""
                               placeholder="<?= $search_placeholder ?>"/>
                        <button type="submit" name="submit" id="ekklesia360_group_layout_search_submit" value=""><i
                                class="fas fa-search"></i></button>
                    </fieldset>
                </form>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['search_term'])) {
        echo "<h4 class=\"ekklesia360_group_layout_results_heading\"><a href=\"{$baseURL}\"><i class=\"fas fa-times\"></i></a> Search results for \"{$_GET['search_term']}\"</h4>";
    }
        ?>

        <div id="ekklesia360_group_layout_wrap" class="ekklesia360_group_layout_wrap">

            <?php
            //setup pagination
            $pagination = new CustomPagination($content["show"], (isset($page) ? $page : 1), $howmany);
            $pagination->setShowFirstAndLast(true);
            $resultsPagination = $pagination->getResults();
            //output
            if (count($resultsPagination) > 0) {
                ?>

                <div class="ekklesia360_group_layout" data-columncount="<?php echo $column_count; ?>"
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

                        echo "<article>";
                        echo "<div class=\"info\">";
                        if ($show_images && $item['imageurl']) {
                            if ($detail_url) echo "<a href=\"{$detail_url}?ekklesia360_group_slug={$item['slug']}\">";
                            echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                            if ($detail_url) echo "</a>";
                        }

                        echo "<h4 class=\"ekklesia360_group_layout_heading\">";
                        if ($detail_url) echo "<a href=\"{$detail_url}?ekklesia360_group_slug={$item['slug']}\">";
                        echo "{$item['name']}";
                        if ($detail_url) echo "</a>";
                        echo "</h4>";

                        if ($show_day && $item['dayoftheweek']) {
                            echo "<h6 class=\"ekklesia360_group_layout_meta\">Meeting Day: {$item['dayoftheweek']}</h6>";
                        }
                        if ($show_times && ($item['starttime'] || $item['endtime'])) {
                            echo "<h6 class=\"ekklesia360_group_layout_meta\">Meeting Time: ";
                            if ($item['starttime']) echo "{$item['starttime']}";
                            if ($item['endtime']) echo " - {$item['endtime']}";
                            echo "</h6>";
                        }
                        if ($show_category && $item['category']) {
                            echo "<h6 class=\"ekklesia360_group_layout_meta\">Category: {$item['category']}</h6>";
                        }
                        if ($show_group && $item['group']) {
                            echo "<h6 class=\"ekklesia360_group_layout_meta\">Group: {$item['group']}</h6>";
                        }
                        if ($show_status && $item['groupstatus']) {
                            echo "<h6 class=\"ekklesia360_group_layout_meta\">Status: {$item['groupstatus']}</h6>";
                        }
                        if ($show_childcare) {
                            $childcare = "No";
                            if ($item['childcareprovided']) {
                                $childcare = "Yes";
                            }
                            echo "<h6 class=\"ekklesia360_group_layout_meta\">Childcare Provided: {$childcare}</h6>";
                        }
                        if ($show_resourcelink && $item['resourcelink']) {
                            $resource_target = "";
                            if ($item['iflinknewwindow']) {
                                $resource_target = " target=\"_blank\"";
                            }
                            echo "<h6 class=\"ekklesia360_group_layout_meta\">Resource: <a href=\"{$item['resourcelink']}\" {$resource_target}>{$item['resourcelink']}</a></h6>";
                        }
                        if ($show_preview && $item['description']) {
                            $item['description'] = substr($item['description'], 0, 110) . " ...";
                            echo "<p class=\"ekklesia360_group_layout_preview\">{$item['description']}</p>";
                        }
                        if ($detail_url && $detail_page_button_text) {
                            echo "<p class=\"ekklesia360_group_layout_detail_button\"><a href=\"{$detail_url}?ekklesia360_group_slug={$item['slug']}\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">{$detail_page_button_text}</span></a></p>";
                        }
                        echo "</div>";
                        echo "</article>";
                    }
                    ?>
                </div>
                <?php
                if ($show_pagination) {
                    $paginationOutput = '<p id="ekklesia360_group_layout_pagination" class="ekklesia360_pagination">' . $pagination->getLinks($_GET) . '</p>';
                    $paginationOutput = str_replace('page=', 'ekklesia360_group_layout_page=', $paginationOutput);
                    //if complexity grows consider http_build_query

                    if (isset($_GET['search_term'])) {
                        $paginationOutput = str_replace('?', "?search_term={$_GET['search_term']}&", $paginationOutput);
                    }

                    //add group
                    if (isset($_GET['group'])) {
                        $paginationOutput = str_replace('?', "?group={$_GET['group']}&", $paginationOutput);
                    }
                    //add category
                    if (isset($_GET['category'])) {
                        $paginationOutput = str_replace('?', "?category={$_GET['category']}&", $paginationOutput);
                    }
                    //add1 category
                    if (isset($_GET['category_add1'])) {
                        $paginationOutput = str_replace('?', "?category_add1={$_GET['category_add1']}&", $paginationOutput);
                    }
                    //add2 category
                    if (isset($_GET['category_add2'])) {
                        $paginationOutput = str_replace('?', "?category_add2={$_GET['category_add2']}&", $paginationOutput);
                    }
                    //add3 category
                    if (isset($_GET['category_add3'])) {
                        $paginationOutput = str_replace('?', "?category_add3={$_GET['category_add3']}&", $paginationOutput);
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
        <script>
            <?php if(count($_GET)): ?>
            const id = 'ekklesia360_group_layout_filters';
            const yOffset = - <?= $sticky_space ?>;
            const element = document.getElementById( id );
            const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
            window.scrollTo( {top: y, behavior: 'smooth'} );
            <?php endif; ?>
            function filterEkklesia360Groups( val ) {
                document.getElementById( 'ekklesia360_group_layout_form' ).submit();
            }
        </script>
        <?php
    }

    /**
     * only searches for the expanded category filtering options.  all other options handled within api
     *
     * @param $groups
     * @param $categories
     * @return array
     */
    private function searchArray($groups=array(), $categories=array()){
        $results = array();
        foreach ($groups as $group)
        {
            $pieces = explode(", ", $group["category"]);
            $matches = array_intersect($pieces,$categories);
            if($matches && count($matches > 0))
            {
                $results[] = $group;
            }
        }
        return $results;
    }
}