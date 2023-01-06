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
            'show_image'                      => false,
            'show_category'                   => false,
            'show_group'                      => false,
            'show_coordinator'                => false,
            'category'                        => 'all',
            'group'                           => 'all',
            'show_preview'                    => false,
            'detail_page_button_text'         => '',
            'detail_page'                     => '',
            'howmany'                         => 9,
            'column_count'                    => 3,
            'column_count_tablet'             => 2,
            'column_count_mobile'             => 1,
            'show_pagination'                 => false,
            'show_images'                     => false,
            'show_day'                        => false,
            'show_times'                      => false,
            'show_status'                     => false,
            'show_childcare'                  => false,
            'show_resourcelink'               => false,
            'sticky_space'                    => 0,
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $cms        = $this->monkCMS;
        $detail_url = $settings['detail_page'] ? $settings['detail_page'] : false; // TODO - wp to cloud, get the page url

        $content    = $cms->get([
            'module'        => 'smallgroup',
            'display'       => 'list',
            'order'         => 'recent',
            'emailencode'   => 'no',
            'howmany'       => $howmany,
            'page'          => isset($_GET['ekklesia360_group_list_page']) ? $_GET['ekklesia360_group_list_page'] : 1,
            'find_category' => $category == 'all' ? '' : $category,
            'find_group'    => $group == 'all' ? '' : $group,
            'show'          => "__endtime format='g:ia'__",
            'after_show'    => '__pagination__'
        ]);

?>


        <?php //output
        if (!empty($content['show'])) {
        ?>

            <div class="brz-groupList__container" data-columncount="<?php echo $column_count; ?>" data-columncount-tablet="<?php echo $column_count_tablet; ?>" data-columncount-mobile="<?php echo $column_count_mobile; ?>">
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

                    echo "<div class='brz-groupList__item'>";
                    if ($show_images && $item['imageurl']) {
                        if ($detail_url) echo "<a href=\"{$detail_url}?ekklesia360_group_slug={$item['slug']}\">";
                        echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                        if ($detail_url) echo "</a>";
                    }

                    echo "<h4 class=\"brz-groupList__item--meta--title\">";
                    if ($detail_url) echo "<a href=\"{$detail_url}?ekklesia360_group_slug={$item['slug']}\">";
                    echo "{$item['name']}";
                    if ($detail_url) echo "</a>";
                    echo "</h4>";

                    if ($show_day && $item['dayoftheweek']) {
                        echo "<h6 class=\"brz-groupList__item--meta\">Meeting Day: {$item['dayoftheweek']}</h6>";
                    }
                    if ($show_times && ($item['starttime'] || $item['endtime'])) {
                        echo "<h6 class=\"brz-groupList__item--meta\">Meeting Time: ";
                        if ($item['starttime']) echo "{$item['starttime']}";
                        if ($item['endtime']) echo " - {$item['endtime']}";
                        echo "</h6>";
                    }
                    if ($show_category && $item['category']) {
                        echo "<h6 class=\"brz-groupList__item--meta\">Category: {$item['category']}</h6>";
                    }
                    if ($show_group && $item['group']) {
                        echo "<h6 class=\"brz-groupList__item--meta\">Group: {$item['group']}</h6>";
                    }
                    if ($show_status && $item['groupstatus']) {
                        echo "<h6 class=\"brz-groupList__item--meta\">Status: {$item['groupstatus']}</h6>";
                    }
                    if ($show_childcare) {
                        $childcare = "No";
                        if ($item['childcareprovided']) {
                            $childcare = "Yes";
                        }
                        echo "<h6 class=\"brz-groupList__item--meta\">Childcare Provided: {$childcare}</h6>";
                    }
                    if ($show_resourcelink && $item['resourcelink']) {
                        $resource_target = "";
                        if ($item['iflinknewwindow']) {
                            $resource_target = " target=\"_blank\"";
                        }
                        echo "<h6 class=\"brz-groupList__item--meta--link\"><span class='brz-groupList__item--meta'>Resource: </span><a href=\"{$item['resourcelink']}\" {$resource_target}>{$item['resourcelink']}</a></h6>";
                    }
                    if ($show_preview && $item['description']) {
                        $item['description'] = substr($item['description'], 0, 110) . " ...";
                        echo "<div class=\"brz-groupList__item--meta--preview\">{$item['description']}</div>";
                        
                    }
                    if ($detail_url && $detail_page_button_text) {
                        echo "<div class=\"brz-ministryBrands__item--meta--button\"><a href=\"{$detail_url}?ekklesia360_group_slug={$item['slug']}\">{$detail_page_button_text}</a></div>";
                    }
                    echo "</div>";
                }
                ?>
            </div>
            <?php
            if ($show_pagination && $content['after_show']['pagination']) {
                $content['after_show']['pagination'] = str_replace('id="pagination"', 'id="ekklesia360_group_list_pagination" class="brz-ministryBrands__pagination"', $content['after_show']['pagination']);
                $content['after_show']['pagination'] = str_replace('page=', 'ekklesia360_group_list_page=', $content['after_show']['pagination']);
                echo $content['after_show']['pagination'];
            }
        } //no output
        else {
            ?>

            <p>There are no groups available.</p>

        <?php
        }
        ?>
<?php
    }
}
