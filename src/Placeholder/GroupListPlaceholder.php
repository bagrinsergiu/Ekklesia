<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class GroupListPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_group_list';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'show_image'                      => true,
            'show_category'                   => true,
            'show_group'                      => true,
            'show_coordinator'                => true,
            'category'                        => 'all',
            'group'                           => 'all',
            'show_preview'                    => true,
            'detail_page_button_text'         => false,
            'detail_page'                     => false,
            'howmany'                         => 9,
            'column_count'                    => 3,
            'column_count_tablet'             => 2,
            'column_count_mobile'             => 1,
            'show_pagination'                 => true,
            'show_images'                     => true,
            'show_day'                        => true,
            'show_times'                      => true,
            'show_status'                     => true,
            'show_childcare'                  => true,
            'show_resourcelink'               => true,
            'sticky_space'                    => 0,
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $cms        = $this->monkCMS;
        $detail_url = $settings['detail_page'] ? home_url($settings['detail_page']) : false;
        $content    = $cms->get([
            'module'        => 'smallgroup',
            'display'       => 'list',
            'order'         => 'recent',
            'emailencode'   => 'no',
            'howmany'       => $howmany,
            'page'          => isset($_GET['ekklesia360_group_list_page']) ? $_GET['ekklesia360_group_list_page'] : 1,
            'find_category' => $category == 'all' ? '' : $category,
            'find_group'    => $group == 'all' ? '' : $group,
            'show'          => "__starttime format='g:ia'__",
            'show'          => "__endtime format='g:ia'__",
            'after_show'    => '__pagination__'
        ]);

        ?>

        <div id="ekklesia360_group_list_wrap" class="ekklesia360_group_list_wrap">

            <?php //output
            if (!empty($content['show'])) {
                ?>

                <div class="ekklesia360_group_list" data-columncount="<?php echo $column_count; ?>"
                     data-columncount-tablet="<?php echo $column_count_tablet; ?>"
                     data-columncount-mobile="<?php echo $column_count_mobile; ?>">
                    <?php
                    foreach ($content['show'] as $key => $item) {
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

                        echo "<h4 class=\"ekklesia360_group_list_heading\">";
                        if ($detail_url) echo "<a href=\"{$detail_url}?ekklesia360_group_slug={$item['slug']}\">";
                        echo "{$item['name']}";
                        if ($detail_url) echo "</a>";
                        echo "</h4>";

                        if ($show_day && $item['dayoftheweek']) {
                            echo "<h6 class=\"ekklesia360_group_list_meta\">Meeting Day: {$item['dayoftheweek']}</h6>";
                        }
                        if ($show_times && ($item['starttime'] || $item['endtime'])) {
                            echo "<h6 class=\"ekklesia360_group_list_meta\">Meeting Time: ";
                            if ($item['starttime']) echo "{$item['starttime']}";
                            if ($item['endtime']) echo " - {$item['endtime']}";
                            echo "</h6>";
                        }
                        if ($show_category && $item['category']) {
                            echo "<h6 class=\"ekklesia360_group_list_meta\">Category: {$item['category']}</h6>";
                        }
                        if ($show_group && $item['group']) {
                            echo "<h6 class=\"ekklesia360_group_list_meta\">Group: {$item['group']}</h6>";
                        }
                        if ($show_status && $item['groupstatus']) {
                            echo "<h6 class=\"ekklesia360_group_list_meta\">Status: {$item['groupstatus']}</h6>";
                        }
                        if ($show_childcare) {
                            $childcare = "No";
                            if ($item['childcareprovided']) {
                                $childcare = "Yes";
                            }
                            echo "<h6 class=\"ekklesia360_group_list_meta\">Childcare Provided: {$childcare}</h6>";
                        }
                        if ($show_resourcelink && $item['resourcelink']) {
                            $resource_target = "";
                            if ($item['iflinknewwindow']) {
                                $resource_target = " target=\"_blank\"";
                            }
                            echo "<h6 class=\"ekklesia360_group_list_meta\">Resource: <a href=\"{$item['resourcelink']}\" {$resource_target}>{$item['resourcelink']}</a></h6>";
                        }
                        if ($show_preview && $item['description']) {
                            $item['description'] = substr($item['description'], 0, 110) . " ...";
                            echo "<p class=\"ekklesia360_group_list_preview\">{$item['description']}</p>";
                        }
                        if ($detail_url && $detail_page_button_text) {
                            echo "<p class=\"ekklesia360_group_list_detail_button\"><a href=\"{$detail_url}?ekklesia360_group_slug={$item['slug']}\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">{$detail_page_button_text}</span></a></p>";
                        }
                        echo "</div>";
                        echo "</article>";
                    }
                    ?>
                </div>
                <?php
                if ($show_pagination && $content['after_show']['pagination']) {
                    $content['after_show']['pagination'] = str_replace('id="pagination"', 'id="ekklesia360_group_list_pagination" class="ekklesia360_pagination"', $content['after_show']['pagination']);
                    $content['after_show']['pagination'] = str_replace('page=', 'ekklesia360_group_list_page=', $content['after_show']['pagination']);
                    echo $content['after_show']['pagination'];
                }
                if (count($_GET)) {
                    echo "<script>";
                    echo "const id = 'ekklesia360_group_list_wrap';";
                    echo "const yOffset = -" . $sticky_space . ";";
                    echo "const element = document.getElementById(id);";
                    echo "const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;";
                    echo "window.scrollTo({top: y, behavior: 'smooth'});";
                    echo "</script>";
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
}