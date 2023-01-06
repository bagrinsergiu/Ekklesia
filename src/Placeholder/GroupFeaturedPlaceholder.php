<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class GroupFeaturedPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_group_featured';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'show_image'              => false,
            'show_category'           => false,
            'show_group'              => false,
            'show_day'                => false,
            'show_times'              => false,
            'show_status'             => false,
            'show_childcare'          => false,
            'show_resourcelink'       => false,
            'show_preview'            => false,
            'group_latest'            => false,
            'group_recent_list'       => false,
            'group_slug'              => false,
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
        $detail_url        = $settings['detail_page'] ? $settings['detail_page'] : false; // TODO - wp to cloud, get the page url

        if ($group_latest) {
            $content1 = $cms->get([
                'module'        => 'smallgroup',
                'display'       => 'list',
                'order'         => 'recent',
                'howmany'       => 1,
                'find_category' => $category,
                'find_group'    => $group,
                'emailencode'   => 'no',
                'show'          => "__endtime format='g:ia'__",
            ]);
            $content = empty($content1['show'][0]) ? [] : $content1['show'][0];
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
                'show'        => "__endtime format='g:ia'__",
            ])['show'];
        }
?>


        <?php //output
        if (isset($content) && count($content) > 0) {
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

            <div class="brz-groupFeatured__item">
                <?php
                echo "<h2 class=\"brz-groupFeatured__item--meta--title\">{$item['name']}</h2>";
                if ($show_day && $item['dayoftheweek']) {
                    echo "<h5 class=\"brz-groupFeatured__item--meta--date\">Meeting Day: {$item['dayoftheweek']}</h5>";
                }
                if ($show_times && ($item['starttime'] || $item['endtime'])) {
                    echo "<h5 class=\"brz-groupFeatured__item--meta--date\">Meeting Time: ";
                    if ($item['starttime']) echo "{$item['starttime']}";
                    if ($item['endtime']) echo " - {$item['endtime']}";
                    echo "</h5>";
                }

                if ($show_image && $item['imageurl']) {
                    echo "<img src=\"{$item['imageurl']}\" alt=\"\" />";
                }

                if ($show_category && $item['category']) {
                    echo "<h6 class=\"brz-groupFeatured__item--meta\">Category: {$item['category']}</h6>";
                }
                if ($show_group && $item['group']) {
                    echo "<h6 class=\"brz-groupFeatured__item--meta\">Group: {$item['group']}</h6>";
                }
                if ($show_status && $item['groupstatus']) {
                    echo "<h6 class=\"brz-groupFeatured__item--meta\">Status: {$item['groupstatus']}</h6>";
                }
                if ($show_childcare) {
                    $childcare = "No";
                    if ($item['childcareprovided']) {
                        $childcare = "Yes";
                    }
                    echo "<h6 class=\"brz-groupFeatured__item--meta\">Childcare Provided: {$childcare}</h6>";
                }
                if ($show_resourcelink && $item['resourcelink']) {
                    $resource_target = "";
                    if ($item['iflinknewwindow']) {
                        $resource_target = " target=\"_blank\"";
                    }
                    echo "<h6 class=\"brz-groupFeatured__item--meta\">Resource: <a href=\"{$item['resourcelink']}\" {$resource_target}>{$item['resourcelink']}</a></h6>";
                }
                if ($show_preview && $item['description']) {
                    echo "<div class=\"brz-groupFeatured__item--meta--preview\">{$item['description']}</div>";
                }

                if ($detail_url && $detail_page_button_text) {
                    echo "<p class=\"brz-ministryBrands__item--meta--button\"><a href=\"{$detail_url}?ekklesia360_group_slug={$item['slug']}\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">{$detail_page_button_text}</span></a></p>";
                }

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
<?php
    }
}
