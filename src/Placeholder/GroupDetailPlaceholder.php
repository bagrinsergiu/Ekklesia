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
        $cms      = $this->monkCMS;

        extract($settings);

        if (isset($_GET['ekk-slug'])) {
            $slug = $_GET['ekk-slug'];
        } elseif ($groups_recent) {
            $slug = $groups_recent;
        } else {
            $recent = $cms->get([
                'module'      => 'smallgroup',
                'display'     => 'list',
                'order'       => 'recent',
                'howmany'     => 1,
                'emailencode' => 'no',
            ]);

            $slug = isset($recent['show'][0]['slug']) ? $recent['show'][0]['slug'] : '';
        }

        $content = $cms->get([
            'module'      => 'smallgroup',
            'display'     => 'detail',
            'find'        => $slug,
            'show'        => "__endtime format='g:ia'__",
            'emailencode' => 'no',
        ]);
?>


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

            <?php
            echo "<div class='brz-groupDetail__item'>";
            // if ($show_title) {
                echo "<h2 class=\"brz-groupDetail__item--meta--title\">{$item['name']}</h2>";
            // }
            if ($show_day && $item['dayoftheweek']) {
                echo "<h5 class=\"brz-groupDetail__item--meta--date\">";
                if ($show_meta_headings) echo "Meeting Day: ";
                echo "{$item['dayoftheweek']}";
                echo "</h5>";
            }
            if ($show_times && ($item['starttime'] || $item['endtime'])) {
                echo "<h5 class=\"brz-groupDetail__item--meta--date\">";
                if ($show_meta_headings) echo "Meeting Time: ";
                if ($item['starttime']) echo "{$item['starttime']}";
                if ($item['endtime']) echo " - {$item['endtime']}";
                echo "</h5>";
            }

            if ($show_image && $item['imageurl']) {
                echo "<img src=\"{$item['imageurl']}\" alt=\"\" />";
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

            echo '<div class="brz-ministryBrands__item--meta--links brz-ministryBrands__item--meta--links--previous">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" class="brz-icon-svg align-[initial]"><path d="M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z"></path></svg>
            Previous Page</div>';
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
