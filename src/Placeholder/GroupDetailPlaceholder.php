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
            'show_image'         => false,
            'show_title'         => false,
            'show_category'      => false,
            'show_group'         => false,
            'show_meta_headings' => false,
            'show_day'           => false,
            'show_times'         => false,
            'show_status'        => false,
            'show_childcare'     => false,
            'show_resourcelink'  => false,
            'show_content'       => false,
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


        <?php //output
          if (count($content['show']) > 0) {
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

            <?php
            echo "<div class='brz-groupDetail__item'>";
            // if ($show_title) {
                echo "<h2 class=\"brz-groupDetail__item--meta--title\">{$item['name']}</h2>";
            // }
            if ($show_day && $item['dayoftheweek']) {
                echo "<h5 class=\"brz-groupDetail__item--meta--time\">";
                if ($show_meta_headings) echo "Meeting Day: ";
                echo "{$item['dayoftheweek']}";
                echo "</h5>";
            }
            if ($show_times && ($item['starttime'] || $item['endtime'])) {
                echo "<h5 class=\"brz-groupDetail__item--meta--time\">";
                if ($show_meta_headings) echo "Meeting Time: ";
                if ($item['starttime']) echo "{$item['starttime']}";
                if ($item['endtime']) echo " - {$item['endtime']}";
                echo "</h5>";
            }

            if ($show_image && $item['imageurl']) {
                echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
            }

            if ($show_category && $item['category']) {
                echo "<h6 class=\"brz-groupDetail__item--meta\">";
                if ($show_meta_headings) echo "Category: ";
                echo "{$item['category']}";
                if ($show_meta_headings) echo "</h6>";
            }
            if ($show_group && $item['group']) {
                echo "<h6 class=\"brz-groupDetail__item--meta\">";
                if ($show_meta_headings) echo "Group: ";
                echo "{$item['group']}";
                echo "</h6>";
            }
            if ($show_status && $item['groupstatus']) {
                echo "<h6 class=\"brz-groupDetail__item--meta\">";
                if ($show_meta_headings) echo "Status: ";
                echo "{$item['groupstatus']}";
                echo "</h6>";
            }
            if ($show_childcare) {
                $childcare = "No";
                if ($item['childcareprovided']) {
                    $childcare = "Yes";
                }
                echo "<h6 class=\"brz-groupDetail__item--meta\">";
                if ($show_meta_headings) echo "Childcare Provided: ";
                echo "{$childcare}";
                echo "</h6>";
            }
            if ($show_resourcelink && $item['resourcelink']) {
                $resource_target = "";
                if ($item['iflinknewwindow']) {
                    $resource_target = " target=\"_blank\"";
                }
                echo "<h6 class=\"brz-groupDetail__item--meta\">";
                if ($show_meta_headings) echo "Resource: ";
                echo "<a href=\"{$item['resourcelink']}\" {$resource_target}>{$item['resourcelink']}</a>";
                echo "</h6>";
            }
            if ($show_content && $item['description']) {
                echo "<div class=\"brz-groupDetail__item--meta--preview\">{$item['description']}</div>";
            }

            echo "<p class=\"brz-ministryBrands__item--meta--links brz-ministryBrands__item--meta--links--previous\">Previous Page</p>";

            echo "</div>";
            ?>
        <?php
        } else {
        ?>

            <p>There is no group available.</p>

        <?php
        }
        ?>
<?php
    }
}
