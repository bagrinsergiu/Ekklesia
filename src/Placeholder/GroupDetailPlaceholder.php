<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class GroupDetailPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_group_detail';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'show_image'         => true,
            'show_title'         => true,
            'show_category'      => true,
            'show_group'         => true,
            'show_meta_headings' => true,
            'show_day'           => true,
            'show_times'         => true,
            'show_status'        => true,
            'show_childcare'     => true,
            'show_resourcelink'  => true,
            'show_content'       => true,
            'groups_recent'      => false,
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $cms    = $this->monkCMS;
        $recent = $cms->get([
            'module'      => 'smallgroup',
            'display'     => 'list',
            'order'       => 'recent',
            'howmany'     => 1,
            'emailencode' => 'no',
        ]);

        if (isset($_GET['ekklesia360_group_slug'])) {
            $slug = $_GET['ekklesia360_group_slug'];
        } elseif ($groups_recent) {
            $slug = $groups_recent;
        } else {
            $slug = isset($recent['show'][0]['slug']) ? $recent['show'][0]['slug'] : '';
        }

        $content = $cms->get([
            'module'      => 'smallgroup',
            'display'     => 'detail',
            'find'        => $slug,
            'show'        => "__starttime format='g:ia'__",
            'show'        => "__endtime format='g:ia'__",
            'emailencode' => 'no',
        ]);
        ?>

        <div class="ekklesia360_group_detail_wrap">

            <?php //output
            if (!empty($content['show'])) {
                $item = $content['show'];

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

                <div class="ekklesia360_group_detail">
                    <?php
                    echo "<article>";
                    echo "<div class=\"info\">";
                    if ($show_title) {
                        echo "<h2 class=\"ekklesia360_group_detail_heading\">{$item['name']}</h2>";
                    }
                    if ($show_day && $item['dayoftheweek']) {
                        echo "<h5 class=\"ekklesia360_group_detail_times\">";
                        if ($show_meta_headings) echo "Meeting Day: ";
                        echo "{$item['dayoftheweek']}";
                        echo "</h5>";
                    }
                    if ($show_times && ($item['starttime'] || $item['endtime'])) {
                        echo "<h5 class=\"ekklesia360_group_detail_times\">";
                        if ($show_meta_headings) echo "Meeting Time: ";
                        if ($item['starttime']) echo "{$item['starttime']}";
                        if ($item['endtime']) echo " - {$item['endtime']}";
                        echo "</h5>";
                    }

                    if ($show_image && $item['imageurl']) {
                        echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                    }

                    if ($show_category && $item['category']) {
                        echo "<h6 class=\"ekklesia360_group_detail_meta\">";
                        if ($show_meta_headings) echo "Category: ";
                        echo "{$item['category']}";
                        if ($show_meta_headings) echo "</h6>";
                    }
                    if ($show_group && $item['group']) {
                        echo "<h6 class=\"ekklesia360_group_detail_meta\">";
                        if ($show_meta_headings) echo "Group: ";
                        echo "{$item['group']}";
                        echo "</h6>";
                    }
                    if ($show_status && $item['groupstatus']) {
                        echo "<h6 class=\"ekklesia360_group_detail_meta\">";
                        if ($show_meta_headings) echo "Status: ";
                        echo "{$item['groupstatus']}";
                        echo "</h6>";
                    }
                    if ($show_childcare) {
                        $childcare = "No";
                        if ($item['childcareprovided']) {
                            $childcare = "Yes";
                        }
                        echo "<h6 class=\"ekklesia360_group_detail_meta\">";
                        if ($show_meta_headings) echo "Childcare Provided: ";
                        echo "{$childcare}";
                        echo "</h6>";
                    }
                    if ($show_resourcelink && $item['resourcelink']) {
                        $resource_target = "";
                        if ($item['iflinknewwindow']) {
                            $resource_target = " target=\"_blank\"";
                        }
                        echo "<h6 class=\"ekklesia360_group_detail_meta\">";
                        if ($show_meta_headings) echo "Resource: ";
                        echo "<a href=\"{$item['resourcelink']}\" {$resource_target}>{$item['resourcelink']}</a>";
                        echo "</h6>";
                    }
                    if ($show_content && $item['description']) {
                        echo "<div class=\"ekklesia360_group_detail_content\">{$item['description']}</div>";
                    }

                    echo "<p class=\"ekklesia360_group_detail_previous\"><a href=\"javascript:history.back();\"><i class=\"fas fa-angle-left\"></i> Previous Page</a></p>";

                    echo "</div>";
                    echo "</article>";
                    ?>
                </div>
                <?php
            } else {
                ?>

                <p>There is no group available.</p>

                <?php
            }
            ?>
        </div>
        <?php
    }
}