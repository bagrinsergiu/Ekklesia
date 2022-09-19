<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;
use DateInterval;
use DatePeriod;
use DateTime;

class EventLayoutPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_event_layout';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'show_featured_view'           => true,
            'view_order_featured'          => 1,
            'view_featured_heading'        => 'Featured Events',
            'howmanyfeatured'              => 9,
            'column_count_featured'        => 3,
            'column_count_featured_tablet' => 2,
            'column_count_featured_mobile' => 1,
            'show_images_featured'         => true,
            'show_title_featured'          => true,
            'show_date_featured'           => true,
            'show_preview_featured'        => true,
            'show_list_view'               => true,
            'view_order_list'              => 2,
            'view_list_heading'            => 'Events List',
            'show_calendar_view'           => true,
            'view_order_calendar'          => 3,
            'view_calendar_heading'        => 'Full Calendar',
            'howmanymonths'                => 3,
            'detail_page'                  => false,
            'sticky_space'                 => 0,
            'parent_category'              => '',
            'category_filter_list'         => '',
            'category_filter_list_add1'    => '',
            'category_filter_list_add2'    => '',
            'show_category_filter'         => true,
            'category_filter_parent'       => '',
            'category_filter_heading'      => 'Category',
            'show_category_filter_add1'    => false,
            'category_filter_parent_add1'  => '',
            'category_filter_heading_add1' => 'Category',
            'show_category_filter_add2'    => false,
            'category_filter_parent_add2'  => '',
            'category_filter_heading_add2' => 'Category',
            'show_category_filter_add3'    => true,
            'category_filter_parent_add3'  => '',
            'category_filter_list_add3'    => '',
            'category_filter_heading_add3' => 'Category',
            'show_group_filter'            => false,
            'group_filter_heading'         => 'Group',
            'show_search'                  => true,
            'search_placeholder'           => 'Search',
            'featuredActive'               => '',
            'listActive'                   => '',
            'calendarActive'               => '',
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $cms             = $this->monkCMS;
        $isPreview       = true; // TODO - check from wordpress and from cloud
        $baseURL         = (strtok($_SERVER["REQUEST_URI"], '?') !== FALSE) ? strtok($_SERVER["REQUEST_URI"], '?') : $_SERVER["REQUEST_URI"];
        $detail_url      = $settings['detail_page'] ? home_url($settings['detail_page']) : false;
        $parent_category = [];
        $calendarStart   = date('Y-m-d');
        $calendarEnd     = date('Y-m-d', strtotime("+{$howmanymonths} months"));
        $date1           = new DateTime($calendarStart);
        $date2           = new DateTime($calendarEnd);
        $diff            = $date1->diff($date2, true);
        $calendarDays    = $diff->format('%a');
        $group_filter    = $_GET['group'] ?? false;

        if ($category_filter_list) {
            $category_filter_list = preg_replace("/\s+/", "", $category_filter_list);
            $category_filter_list = explode(",", $category_filter_list);
            array_push($parent_category, ...$category_filter_list);
        }

        if ($category_filter_list_add1) {
            $category_filter_list_add1 = preg_replace("/\s+/", "", $category_filter_list_add1);
            $category_filter_list_add1 = explode(",", $category_filter_list_add1);
            array_push($parent_category, ...$category_filter_list_add1);
        }

        if ($category_filter_list_add2) {
            $category_filter_list_add2 = preg_replace("/\s+/", "", $category_filter_list_add2);
            $category_filter_list_add2 = explode(",", $category_filter_list_add2);
            array_push($parent_category, ...$category_filter_list_add2);
        }

        if ($category_filter_list_add3) {
            $category_filter_list_add3 = preg_replace("/\s+/", "", $category_filter_list_add3);
            $category_filter_list_add3 = explode(",", $category_filter_list_add3);
            array_push($parent_category, ...$category_filter_list_add3);
        }

        $parent_category = $parent_category ? implode(",", $parent_category) : '';

        //views
        if (isset($_GET['view'])) {
            $view = $_GET['view'];
        } else {
            $orderArr = [];
            if ($show_featured_view) {
                $orderArr[$view_order_featured] = "featured";
            }
            if ($show_list_view) {
                $orderArr[$view_order_list] = "list";
            }
            if ($show_calendar_view) {
                $orderArr[$view_order_calendar] = "calendar";
            }
            ksort($orderArr);
            $orderArr = array_unique($orderArr);
            $view = reset($orderArr);
        }

        //activate featured view
        ${$view . "Active"} = "active";

        $categories = $cms->get([
            'module'  => 'event',
            'display' => 'categories'
        ]);

        $categories_parent = $cms->get([
            'module'          => 'event',
            'display'         => 'categories',
            'parent_category' => $parent_category,
        ]);

        $groups = $cms->get([
            'module'  => 'group',
            'display' => 'list'
        ]);

        //test search first
        if (isset($_GET['search_term'])) {
            $content    = [];
            $search_arr = $cms->get([
                'module'        => 'search',
                'display'       => 'results',
                'howmany'       => $howmany,
                'find_category' => $parent_category,
                'keywords'      => $_GET['search_term'],
                'find_module'   => 'event',
                'hide_module'   => 'media',
                'after_show'    => '__pagination__'
            ]);

            foreach ($search_arr['show'] as $search) {
                //$search['slug'] = str_replace('/event/','',$search['url']);
                $item = $cms->get([
                    'module'      => 'event',
                    'display'     => 'detail',
                    'emailencode' => 'no',
                    'find'        => $search['slug'],
                ]);
                $content['show'][] = $item['show'];
            }
            if ($search_arr['after_show']['pagination']) {
                $content['after_show']['pagination'] = $search_arr['after_show']['pagination'];
            }
        } //if no search module api
        else {

            if ($view == "featured") {
                $content = $cms->get([
                    'module'               => 'event',
                    'display'              => 'list',
                    'emailencode'          => 'no',
                    'features'             => 'features',
                    'howmany'              => $howmanyfeatured,
                    'find_parent_category' => $parent_category
                ]);
            } else {
                $content = $cms->get([
                    'module'               => 'event',
                    'display'              => 'list',
                    'emailencode'          => 'no',
                    'recurring'            => 'yes',
                    'repeatevent'          => 'yes',
                    'groupby'              => 'day',
                    'howmanydays'          => $calendarDays,
                    'find_parent_category' => $parent_category,
                    'find_group'           => $group_filter
                ]);
            }

            //filter categories separately since there can be more than 1 category filter
            if (isset($_GET["category"])) {
                $content["show"] = self::searchArray($content["show"], $_GET["category"]);
            }
            if (isset($_GET["category_add1"])) {
                $content["show"] = self::searchArray($content["show"], $_GET["category_add1"]);
            }
            if (isset($_GET["category_add2"])) {
                $content["show"] = self::searchArray($content["show"], $_GET["category_add2"]);
            }
            if (isset($_GET["category_add3"])) {
                $content["show"] = self::searchArray($content["show"], $_GET["category_add3"]);
            }
        }
        ?>

        <div id="ekklesia360_event_layout_view" class="ekklesia360_event_layout_view">
            <ul>
                <?php if ($show_featured_view): ?>
                    <li class="featured <?= $featuredActive ?>" data-order="<?= $view_order_featured ?>"><a
                        href="<?= $baseURL ?>?view=featured"><?= $view_featured_heading ?></a></li><?php endif; ?>
                <?php if ($show_list_view): ?>
                    <li class="<?= $listActive ?>" data-order="<?= $view_order_list ?>"><a
                        href="<?= $baseURL ?>?view=list"><?= $view_list_heading ?></a></li><?php endif; ?>
                <?php if ($show_calendar_view): ?>
                    <li class="<?= $calendarActive ?>" data-order="<?= $view_order_calendar ?>"><a
                        href="<?= $baseURL ?>?view=calendar"><?= $view_calendar_heading ?></a></li><?php endif; ?>
            </ul>
        </div>

        <?php if ($view != "featured" || $isPreview): //hide from featured view ?>
        <div id="ekklesia360_event_layout_filters" class="ekklesia360_event_layout_filters">
            <form id="ekklesia360_event_layout_form" name="ekklesia360_event_layout_form" action="<?= $baseURL ?>">

                <?php if ($show_group_filter && count($groups['show']) > 0): ?>
                    <select name="group" class='sorter' onchange='filterEkklesia360Events()'>
                        <option value=""><?= $group_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        foreach ($groups['show'] as $group) {
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
                    <select name="category" class='sorter' onchange='filterEkklesia360Events()'>
                        <option value=""><?= $category_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        if (is_array($category_filter_list)) {
                            foreach ($category_filter_list as $category) {
                                $catKey = array_search($category, array_column($categories['show'], "slug"));
                                $catMatch = $categories['show'][$catKey];
                                if ($catKey !== FALSE) {
                                    echo "<option value=\"{$catMatch['slug']}\"";
                                    if (isset($_GET['category']) && $_GET['category'] == $catMatch['slug']) {
                                        echo " selected";
                                    }
                                    echo ">{$catMatch['name']}</option>";
                                }
                            }
                        } //since this is the main category filter this will always show
                        elseif ($category_filter_parent) {
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
                if ($show_category_filter_add1 && ($category_filter_parent_add1 != "" || is_array($category_filter_list_add1))): ?>
                    <select name="category_add1" class='sorter' onchange='filterEkklesia360Events()'>
                        <option value=""><?= $category_filter_heading_add1 ?></option>
                        <option value="">All</option>
                        <?php
                        if (is_array($category_filter_list_add1)) {
                            foreach ($category_filter_list_add1 as $category) {
                                $catKey = array_search($category, array_column($categories['show'], "slug"));
                                $catMatch = $categories['show'][$catKey];
                                if ($catKey !== FALSE) {
                                    echo "<option value=\"{$catMatch['slug']}\"";
                                    if (isset($_GET['category_add1']) && $_GET['category_add1'] == $catMatch['slug']) {
                                        echo " selected";
                                    }
                                    echo ">{$catMatch['name']}</option>";
                                }
                            }
                        } else {
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
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <?php
                if ($show_category_filter_add2 && ($category_filter_parent_add2 != "" || is_array($category_filter_list_add2))): ?>
                    <select name="category_add2" class='sorter' onchange='filterEkklesia360Events()'>
                        <option value=""><?= $category_filter_heading_add2 ?></option>
                        <option value="">All</option>
                        <?php
                        if (is_array($category_filter_list_add2)) {
                            foreach ($category_filter_list_add2 as $category) {
                                $catKey = array_search($category, array_column($categories['show'], "slug"));
                                $catMatch = $categories['show'][$catKey];
                                if ($catKey !== FALSE) {
                                    echo "<option value=\"{$catMatch['slug']}\"";
                                    if (isset($_GET['category_add2']) && $_GET['category_add2'] == $catMatch['slug']) {
                                        echo " selected";
                                    }
                                    echo ">{$catMatch['name']}</option>";
                                }
                            }
                        } else {
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
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <?php
                if ($show_category_filter_add3 && ($category_filter_parent_add3 != "" || is_array($category_filter_list_add3))): ?>
                    <select name="category_add3" class='sorter' onchange='filterEkklesia360Events()'>
                        <option value=""><?= $category_filter_heading_add3 ?></option>
                        <option value="">All</option>
                        <?php
                        if (is_array($category_filter_list_add3)) {
                            foreach ($category_filter_list_add3 as $category) {
                                $catKey = array_search($category, array_column($categories['show'], "slug"));
                                $catMatch = $categories['show'][$catKey];
                                if ($catKey !== FALSE) {
                                    echo "<option value=\"{$catMatch['slug']}\"";
                                    if (isset($_GET['category_add3']) && $_GET['category_add3'] == $catMatch['slug']) {
                                        echo " selected";
                                    }
                                    echo ">{$catMatch['name']}</option>";
                                }
                            }
                        } else {
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
                        }
                        ?>
                    </select>
                <?php endif; ?>
                <input type="hidden" name="view" value="<?= $view ?>"/>
            </form>

            <?php if ($show_search): ?>
                <form method="get" id="ekklesia360_event_layout_search" name="search" action="<?= $baseURL ?>">
                    <fieldset>
                        <input type="text" id="ekklesia360_event_layout_search_term" name="search_term" value=""
                               placeholder="<?= $search_placeholder ?>"/>
                        <button type="submit" name="submit" id="ekklesia360_event_layout_search_submit" value=""><i
                                class="fas fa-search"></i></button>
                    </fieldset>
                    <input type="hidden" name="view" value="list"/>
                </form>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['search_term'])) {
            echo "<h4 class=\"ekklesia360_event_layout_results_heading\"><a href=\"{$baseURL}?view=list\"><i class=\"fas fa-times\"></i></a> Search results for \"{$_GET['search_term']}\"</h4>";
        }
        ?>
    <?php endif; //end hide from featured
        ?>

        <?php
        //featured view
        if ($show_featured_view && ($view == "featured" || $isPreview)):
            ?>
            <div class="ekklesia360_event_featured_wrap">
                <?php //output
                if (count($content['show']) > 0) {
                    ?>

                    <div class="ekklesia360_event_featured" data-columncount="<?php echo $column_count_featured; ?>"
                         data-columncount-tablet="<?php echo $column_count_featured_tablet; ?>"
                         data-columncount-mobile="<?php echo $column_count_featured_mobile; ?>">
                        <?php
                        foreach ($content['show'] as $key => $item) {
                            //__id__-__eventstart format='Y-m-d'__-__slug__
                            $slugDate = date("Y-m-d", strtotime($item["eventstart"]));
                            $slug = "{$item['id']}-$slugDate-{$item['slug']}";

                            if ($detail_url) {
                                $item["url"] = str_replace('/event/', "{$detail_url}?ekklesia360_event_slug=", $item['url']);
                            }

                            echo "<article>";
                            if ($show_images_featured && $item['imageurl']) {
                                echo "<div class=\"image\">";
                                if ($detail_url) echo "<a href=\"{$item['url']}\" title=\"{$item["title"]}\">";
                                echo "<img src=\"{$item['imageurl']}\" alt=\"\" />";
                                if ($show_preview_featured && $item['preview']) {
                                    $item['preview'] = substr($item['preview'], 0, 75) . " ...";
                                    echo "<div class=\"ekklesia360_event_featured_preview\"><div><span>{$item['preview']}</span></div></div>";
                                }
                                if ($detail_url) echo "</a>";
                                echo "</div>";
                            }

                            echo "<div class=\"info\">";
                            if ($show_title_featured) {
                                echo "<h5 class=\"ekklesia360_event_featured_heading\">";
                                if ($detail_url) echo "<a href=\"{$item['url']}\" title=\"{$item["title"]}\">";
                                echo "{$item['title']}";
                                if ($detail_url) echo "</a>";
                                echo "</h5>";
                            }

                            if ($show_date_featured) {
                                echo "<p class=\"ekklesia360_event_featured_meta\">{$item['eventtimes']}</p>";
                            }
                            echo "</div>";
                            echo "</article>";
                        }
                        ?>
                    </div>
                    <?php
                } //no output
                else {
                    ?>

                    <p>There are no events available.</p>

                    <?php
                }
                ?>
            </div>

        <?php endif; //end featured view
        ?>

        <?php
        //list view
        if ($show_list_view && ($view == "list" || $isPreview)):
            ?>
            <div class="ekklesia360_event_list_wrap">

                <?php //output
                if (count($content['show']) > 0) {
                    //iterate over each event and assign to month and day
                    foreach ($content["show"] as $show) {
                        $grouping_month = date("Y-m", strtotime($show["eventstart"]));
                        $grouping_day = date("Y-m-d", strtotime($show["eventstart"]));
                        $events[$grouping_month][$grouping_day][] = $show;//set first dimension to day and then assign all events as second level to that day
                    }
                    echo "<div class=\"ekklesia360_event_layout_list\">";
                    echo self::draw_list($events, $detail_url);
                    echo "</div>";
                } //no output
                else {
                    ?>

                    <p>There are no events available.</p>

                    <?php
                }
                ?>
            </div>
        <?php endif; //end list view
        ?>

        <?php
        //calendar view
        if ($show_calendar_view && ($view == "calendar" || $isPreview)):
            ?>
            <div class="ekklesia360_event_calendar_wrap">

                <?php //output
                if (count($content['show']) > 0) {
                    //iterate over each event and assign to month and day
                    foreach ($content["show"] as $show) {
                        $grouping_month = date("Y-m", strtotime($show["eventstart"]));
                        $grouping_day = date("Y-m-d", strtotime($show["eventstart"]));
                        $events[$grouping_month][$grouping_day][] = $show;//set first dimension to day and then assign all events as second level to that day
                    }
                    ?>

                    <div class="ekklesia360_event_calendar">
                        <?php
                        echo self::draw_calendar($events, $detail_url);
                        ?>
                    </div>
                    <?php
                } //no output
                else {
                    ?>

                    <p>There are no events available.</p>

                    <?php
                }
                ?>
            </div>
        <?php endif; //end calendar view
        ?>

        <script>
            <?php if(count($_GET)): ?>
            const id = 'ekklesia360_event_layout_view';
            const yOffset = - <?= $sticky_space ?>;
            const element = document.getElementById( id );
            const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
            window.scrollTo( {top: y, behavior: 'smooth'} );
            <?php endif; ?>
            function filterEkklesia360Events( val ) {
                document.getElementById( 'ekklesia360_event_layout_form' ).submit();
            }
        </script>

        <?php
    }

    /*
	searchArray only searches for the expanded category filtering options.  all other options handled within api
	also accounts for how the api reveals categories and category slugs with parent category.
	*/
    private function searchArray($items=array(), $category = ""){
        $results = array();
        foreach ($items as $item)
        {

            $pieces = explode(", ", $item["category"]);
            $categoriesArr = array();
            $count = 1;
            foreach($pieces as $piece)
            {
                $catcall = "category".$count."slug";
                $categoriesArr[] = $item[$catcall];
                $count++;
            }
            if (in_array($category, $categoriesArr)) {
                $results[] = $item;
            }
        }
        return $results;
    }

    //draw list
    private function draw_list($events=null, $detail_url=null){

        $results  	= false;
        $period   	= self::get_period($events);

        $period_arr = iterator_to_array($period);

        $start_month = reset($period_arr);
        $start_month_format = $start_month->format("Y-m");

        $end_month = end($period_arr);
        $end_month_format = $end_month->format("Y-m");

        //iterate each month
        foreach($period as $month)
        {
            //set month formats
            $month_format = $month->format("Y-m");//should match format of initial $events month
            $month_label_format = $month->format("F Y");

            //month pagination set
            $pag_format = $month->format("m-Y");
            $prev_month = date("Y-m", strtotime("1-{$pag_format} -1 month"));
            $next_month = date("Y-m", strtotime("1-{$pag_format} +1 month"));


            //open .month div
            $results .= "<div class=\"ekklesia360_month-view {$month_format}\">";

            //pagination
            $results .= "<div class=\"ekklesia360_layout-pagination\">";
            //prev
            if($month_format === $start_month_format)
            {
                $results .= "<a class=\"previous off\"><i class=\"fas fa-angle-left\"></i></a>";
            }
            else
            {
                $results .= "<a href=\"{$prev_month}\" data-month=\"{$prev_month}\" class=\"previous\"><i class=\"fas fa-angle-left\"></i></a>";
            }
            //heading
            $results .= "<span class=\"heading\">{$month_label_format}</span>";

            //next
            if($month_format === $end_month_format)
            {
                $results .= "<a class=\"next off\"><i class=\"fas fa-angle-right\"></i></a>";
            }
            else
            {
                $results .= "<a href=\"{$next_month}\" data-month=\"{$next_month}\" class=\"next\"><i class=\"fas fa-angle-right\"></i></a>";
            }

            $results .= "</div>";


            //if no output nrf
            if(count($events[$month_format]) < 1)
            {
                $results .= "<h4 class=\"nrf\">There are no events for this month.</h4>";
            }
            //else results
            else
            {
                //iterate grouped day
                foreach($events[$month_format] as $day=>$val)
                {
                    $grouping_day = date("l", strtotime($day));
                    $grouping_date = date("F j, Y", strtotime($day));
                    $results .= "<h3 class=\"grouping\">{$grouping_day} <span>{$grouping_date}</span></h3>";
                    //iterate event
                    foreach($val as $v)
                    {
                        $slugDate = date("Y-m-d", strtotime($v["eventstart"]));
                        $slug = "{$v['id']}-$slugDate-{$v['slug']}";
                        if($detail_url)
                        {
                            $v["url"] = str_replace('/event/',"{$detail_url}?ekklesia360_event_slug=",$v['url']);
                        }
                        $results .= "<article>";
                        $results .= "<div class=\"date\">";
                        $results .= "<div>";
                        $results .= "<span class=\"day\">";
                        $results .= date("d", strtotime($v["eventstart"]));
                        $results .= "</span>";
                        $results .= "<span class=\"month\">";
                        $results .= date("M", strtotime($v["eventstart"]));
                        $results .= "</span>";
                        $results .= "</div>";
                        $results .= "</div>";
                        $results .= "<div class=\"info\">";
                        $results .= "<h5 class=\"ekklesia360_event_layout_heading\">";
                        if($detail_url) $results.= "<a href=\"{$v["url"]}\" title=\"{$v["title"]}\">";
                        $results.= "{$v["title"]}";
                        if($detail_url) $results.= "</a>";
                        $results .= "</h5>";
                        $results .= "<div class=\"meta\">";
                        $results .= "<div class='list-time'>";
                        if($v["isallday"])
                        {
                            $results .= "All Day";
                        }
                        else
                        {
                            $results .= date("l, g:i a", strtotime($v["eventstart"]));
                            $results .= " - ";
                            $results .= date("g:i a", strtotime($v["eventend"]));
                        }

                        if($v["isrecurring"])
                        {
                            $results .= " <i class=\"repeat\">Recurring Event</i>";
                        }
                        $results .= "</div><!-- end .list-time -->";
                        $results .= "</div>";
                        $results .= "</div>";
                        $results .= "</article>";
                    }
                }
            }//end if

            //close .month div
            $results .= "</div>";

        }//end foreach month


        return $results;
    }

    //draw calendar
    private function draw_calendar($events=null, $detail_url=null){
        $results  	= false;
        $period   	= self::get_period($events);

        $period_arr = iterator_to_array($period);

        $start_month = reset($period_arr);
        $start_month_format = $start_month->format("Y-m");

        $end_month = end($period_arr);
        $end_month_format = $end_month->format("Y-m");

        //iterate each month
        foreach($period as $month)
        {
            //set month formats
            $month_format = $month->format("Y-m");//should match format of initial $events month
            $month_format_month = $month->format("m");//month to draw table
            $month_format_year = $month->format("Y");//month to draw table

            $month_label_format = $month->format("F Y");

            //month pagination set
            $pag_format = $month->format("m-Y");
            $prev_month = date("Y-m", strtotime("1-{$pag_format} -1 month"));
            $next_month = date("Y-m", strtotime("1-{$pag_format} +1 month"));


            //open .month div
            $results .= "<div class=\"ekklesia360_month-view {$month_format}\">";

            //pagination
            $results .= "<div class=\"ekklesia360_layout-pagination\">";
            //prev
            if($month_format === $start_month_format)
            {
                $results .= "<a class=\"previous off\"><i class=\"fas fa-angle-left\"></i></a>";
            }
            else
            {
                $results .= "<a href=\"{$prev_month}\" data-month=\"{$prev_month}\" class=\"previous\"><i class=\"fas fa-angle-left\"></i></a>";
            }
            //heading
            $results .= "<span class=\"heading\">{$month_label_format}</span>";

            //next
            if($month_format === $end_month_format)
            {
                $results .= "<a class=\"next off\"><i class=\"fas fa-angle-right\"></i></a>";
            }
            else
            {
                $results .= "<a href=\"{$next_month}\" data-month=\"{$next_month}\" class=\"next\"><i class=\"fas fa-angle-right\"></i></a>";
            }

            $results .= "</div>";


            //if no output nrf
            if(count($events[$month_format]) < 1)
            {
                $results .= "<h4 class=\"nrf\">There are no events for this month.</h4>";
            }
            //else results
            else
            {
                //get table of month and pass/format events
                $results .= self::draw_calendar_table($month_format_month, $month_format_year,$events[$month_format], $detail_url);

            }//end if

            //close .month div
            $results .= "</div>";

        }//end foreach month


        return $results;
    }

    //draws calendar table
    private function draw_calendar_table($month, $year, $events=null, $detail_url=null){

        /* draw table */
        $calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

        /* table headings */
        $headings = array('Sun','Mon','Tues','Wed','Thu','Fri','Sat');

        $calendar.= '<tr class="calendar-row weekdays"><th>'.implode('</th><th>',$headings).'</th></tr>';

        /* days and weeks vars now ... */
        $running_day = date('w',mktime(0,0,0,$month,1,$year));
        $days_in_month = date('t',mktime(0,0,0,$month,1,$year));
        $days_in_this_week = 1;
        $day_counter = 0;
        $dates_array = array();

        /* row for week one */
        $calendar.= '<tr class="calendar-row">';

        /* print "blank" days until the first of the current week */
        for($x = 0; $x < $running_day; $x++):
            $calendar.= '<td class="calendar-day-np"> </td>';
            $days_in_this_week++;
        endfor;

        /* keep going with days.... */
        for($list_day = 1; $list_day <= $days_in_month; $list_day++):

            $cur_date = date('Y-m-d', mktime(0, 0, 0, $month, $list_day, $year));

            $calendar.= '<td class="calendar-day">';
            /* add in the day number */
            $calendar.= '<div class="day-number"><span>'.$list_day.'</span></div>';

            /** QUERY FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
            //$calendar.= str_repeat('<p> </p>',2);
            if (isset($events) && isset($events[$cur_date])) {
                $calendar.= "<ul class=\"calendar-links\">";
                foreach($events[$cur_date] as $v)
                {
                    if($detail_url)
                    {
                        $v["url"] = str_replace('/event/',"{$detail_url}?ekklesia360_event_slug=",$v['url']);
                    }
                    $calendar.= "<li>";
                    $calendar.= "<span class=\"title\">";
                    if($detail_url) $calendar.= "<a href=\"{$v['url']}\" title=\"{$v["title"]}\">";
                    $calendar.= "{$v["title"]}";
                    if($detail_url) $calendar.= "</a>";
                    $calendar.= "</span>";
                    $calendar.= "</li>";
                }
                $calendar.= "<ul>";
            }

            $calendar.= '</td>';
            if($running_day == 6):
                $calendar.= '</tr>';
                if(($day_counter+1) != $days_in_month):
                    $calendar.= '<tr class="calendar-row">';
                endif;
                $running_day = -1;
                $days_in_this_week = 0;
            endif;
            $days_in_this_week++; $running_day++; $day_counter++;
        endfor;

        /* finish the rest of the days in the week */
        if($days_in_this_week < 8):
            for($x = 1; $x <= (8 - $days_in_this_week); $x++):
                $calendar.= '<td class="calendar-day-np"> </td>';
            endfor;
        endif;

        /* final row */
        $calendar.= '</tr>';

        /* end the table */
        $calendar.= '</table>';

        /* all done, return result */
        return $calendar;
    }

    //get period between two months
    private function get_period($events){
        $last 		= key(end($events));
        $first 		= key(reset($events));
        $start    	= (new DateTime($first))->modify('first day of this month');
        $end      	= (new DateTime($last))->modify('first day of next month');
        $interval 	= DateInterval::createFromDateString('1 month');
        $period   	= new DatePeriod($start, $interval, $end);
        return $period;
    }
}