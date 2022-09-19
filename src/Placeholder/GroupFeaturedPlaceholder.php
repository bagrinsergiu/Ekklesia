<?php

namespace BrizyEkklesia\Placeholder\Ekklesia360;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class GroupFeaturedPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_group_featured';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'show_image'              => true,
            'show_category'           => true,
            'show_group'              => true,
            'show_day'                => true,
            'show_times'              => true,
            'show_status'             => true,
            'show_childcare'          => true,
            'show_resourcelink'       => true,
            'show_content'            => true,
            'group_latest'            => true,
            'group_recent_list'       => true,
            'group_slug'              => true,
            'category'                => 'all',
            'group'                   => 'all',
            'detail_page_button_text' => false,
            'detail_page'             => false,
            'slug'                    => false,
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $cms               = $this->monkCMS;
        $group_recent_list = $settings['group_recent_list'] != 'none' ? $settings['group_recent_list'] : '';
        $category          = $settings['category'] != 'all' ? $settings['category'] : '';
        $group             = $settings['group'] != 'all' ? $settings['group'] : '';
        $detail_url        = $settings['detail_page'] ? home_url($settings['detail_page']) : false;

        if ($group_latest) {
            $content = $cms->get([
                'module'        => 'smallgroup',
                'display'       => 'list',
                'order'         => 'recent',
                'howmany'       => 1,
                'find_category' => $category,
                'find_group'    => $group,
                'emailencode'   => 'no',
                'show'          => "__starttime format='g:ia'__",
                'show'          => "__endtime format='g:ia'__",
            ])['show'][0];
        } else {
            if ($group_slug) {
                $slug = $group_slug;
            } elseif ($group_recent_list != '') {
                $slug = $group_recent_list;
            }
        }

        if ($slug) {
            $content = $cms->get([
                'module'      => 'smallgroup',
                'display'     => 'detail',
                'find'        => $slug,
                'emailencode' => 'no',
                'show'        => "__starttime format='g:ia'__",
                'show'        => "__endtime format='g:ia'__",
            ])['show'];
        }
        ?>

        <div class="ekklesia360_group_featured_wrap">

            <?php //output
            if (count($content) > 0) {
                $item = $content;

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
                ?>

                <div class="ekklesia360_group_featured">
                    <?php
                    echo "<article>";
                    echo "<div class=\"info\">";
                    echo "<h2 class=\"ekklesia360_group_featured_heading\">{$item['name']}</h2>";
                    if ($show_day && $item['dayoftheweek']) {
                        echo "<h5 class=\"ekklesia360_group_featured_times\">Meeting Day: {$item['dayoftheweek']}</h5>";
                    }
                    if ($show_times && ($item['starttime'] || $item['endtime'])) {
                        echo "<h5 class=\"ekklesia360_group_featured_times\">Meeting Time: ";
                        if ($item['starttime']) echo "{$item['starttime']}";
                        if ($item['endtime']) echo " - {$item['endtime']}";
                        echo "</h5>";
                    }

                    if ($show_image && $item['imageurl']) {
                        echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                    }

                    if ($show_category && $item['category']) {
                        echo "<h6 class=\"ekklesia360_group_featured_meta\">Category: {$item['category']}</h6>";
                    }
                    if ($show_group && $item['group']) {
                        echo "<h6 class=\"ekklesia360_group_featured_meta\">Group: {$item['group']}</h6>";
                    }
                    if ($show_status && $item['groupstatus']) {
                        echo "<h6 class=\"ekklesia360_group_featured_meta\">Status: {$item['groupstatus']}</h6>";
                    }
                    if ($show_childcare) {
                        $childcare = "No";
                        if ($item['childcareprovided']) {
                            $childcare = "Yes";
                        }
                        echo "<h6 class=\"ekklesia360_group_featured_meta\">Childcare Provided: {$childcare}</h6>";
                    }
                    if ($show_resourcelink && $item['resourcelink']) {
                        $resource_target = "";
                        if ($item['iflinknewwindow']) {
                            $resource_target = " target=\"_blank\"";
                        }
                        echo "<h6 class=\"ekklesia360_group_featured_meta\">Resource: <a href=\"{$item['resourcelink']}\" {$resource_target}>{$item['resourcelink']}</a></h6>";
                    }
                    if ($show_content && $item['description']) {
                        echo "<div class=\"ekklesia360_group_featured_content\">{$item['description']}</div>";
                    }

                    if ($detail_url && $detail_page_button_text) {
                        echo "<p class=\"ekklesia360_group_featured_detail_button\"><a href=\"{$detail_url}?ekklesia360_group_slug={$item['slug']}\" class=\"elementor-button-link elementor-button elementor-size-sm\"><span class=\"elementor-button-text\">{$detail_page_button_text}</span></a></p>";
                    }

                    echo "</div>";
                    echo "</article>";
                    ?>
                </div>
                <?php
            } //no output
            else {
                ?>

                <p>There is no group available.</p>

                <?php
            }
            ?>
        </div>
        <?php
    }
}