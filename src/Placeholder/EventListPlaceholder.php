<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class EventListPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_event_list';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'show_images'             => false,
            'show_title'              => false,
            'show_date'               => false,
            'show_category'           => false,
            'show_group'              => false,
            'show_meta_headings'      => false,
            'category'                => 'all',
            'group'                   => 'all',
            'features'                => '',
            'nonfeatures'             => '',
            'show_preview'            => false,
            'detail_page_button_text' => '',
            'detail_page'             => false,
            'howmany'                 => 9,
            'column_count'            => 3,
            'column_count_tablet'     => 2,
            'column_count_mobile'     => 1,
            'show_pagination'         => false,
            'show_location'           => false,
            'show_registration'       => false,
            'sticky_space'            => 0,
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $cms        = $this->monkCMS;
        $category   = $settings['category'] != 'all' ? $settings['category'] : '';
        $group      = $settings['group'] != 'all' ? $settings['group'] : '';
        $detail_url = $settings['detail_page'] ? get_permalink($settings['detail_page']) : false;
        $page       = isset($_GET['ekklesia360_event_list_page']) ? $_GET['ekklesia360_event_list_page'] : 1;

        if ($features) {
            $nonfeatures = '';
        } elseif ($nonfeatures) {
            $features = '';
        }

        $content = $cms->get([
            'module'        => 'event',
            'display'       => 'list',
            'order'         => 'recent',
            'emailencode'   => 'no',
            'howmany'       => $howmany,
            'page'          => $page,
            'find_category' => $category,
            'find_group'    => $group,
            'features'      => $features,
            'nonfeatures'   => $nonfeatures,
            'after_show'    => '__pagination__'
        ]);
?>


        <?php //output
        if (count($content['show']) > 0) {
        ?>

            <div class="brz-eventList__container" data-columncount="<?php echo $column_count; ?>" data-columncount-tablet="<?php echo $column_count_tablet; ?>" data-columncount-mobile="<?php echo $column_count_mobile; ?>">
                <?php
                foreach ($content['show'] as $key => $item) {
                    //__id__-__eventstart format='Y-m-d'__-__slug__
                    $slugDate = date("Y-m-d", strtotime($item["eventstart"]));
                    $slug = "{$item['id']}-$slugDate-{$item['slug']}";

                    echo "<div class=\"brz-eventList__item\">";
                    if ($show_images && $item['imageurl']) {
                        if ($detail_url) echo "<a class=\"brz-ministryBrands__item--meta--links\" href=\"{$detail_url}?ekklesia360_event_slug={$slug}\">";
                        echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                        if ($detail_url) echo "</a>";
                    }

                    if ($show_title) {
                        echo "<h4 class=\"brz-eventList__item--meta--title\">";
                        if ($detail_url) echo "<a class='brz-ministryBrands__item--meta--links' href=\"{$detail_url}?ekklesia360_event_slug={$slug}\">";
                        echo "{$item['title']}";
                        if ($detail_url) echo "</a>";
                        echo "</h4>";
                    }

                    if ($show_date) {
                        echo "<h5 class=\"brz-eventList__item--meta--date\">Date: {$item['eventtimes']}</h5>";
                    }

                    if ($show_category && $item['category']) {
                        echo "<h6 class=\"brz-eventList__item--meta\">";
                        if ($show_meta_headings) echo "Category: ";
                        echo "{$item['category']}";
                        echo "</h6>";
                    }
                    if ($show_group && $item['group']) {
                        echo "<h6 class=\"brz-eventList__item--meta\">";
                        if ($show_meta_headings) echo "Group: ";
                        echo "{$item['group']}";
                        echo "</h6>";
                    }
                    if ($show_location && $item['location']) {
                        echo "<h6 class=\"brz-eventList__item--meta\">";
                        if ($show_meta_headings) echo "Location: ";
                        echo "{$item['location']}";
                        echo "</h6>";
                        if ($item['fulladdress']) {
                            echo "<h6 class=\"brz-eventList__item--meta\">";
                            if ($show_meta_headings) echo "<span class='brz-eventList__item--meta'>Address: </span>";
                            echo "<a class='brz-ministryBrands__item--meta--links' href=\"http://maps.google.com/maps?q={$item["fulladdress"]}\" target=\"_blank\">{$item['fulladdress']}</a>";
                            echo "</h6>";
                        }
                    }
                    if ($show_registration && ($item['registrationurl'] || $item['externalregistrationurl'])) {
                        if ($item['registrationurl']) {
                            echo "<div class=\"brz-ministryBrands__item--meta--button\"><a href=\"{$item['registrationurl']}\" target=\"_blank\" class=\"brz-button-link brz-button brz-size-sm\">Register</div>";
                        }
                        if ($item['externalregistrationurl']) {
                            echo "<div class=\"brz-ministryBrands__item--meta--button\"><a  href=\"{$item['externalregistrationurl']}\" target=\"_blank\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">Register</span></a></div>";
                        }
                    }
                    if ($show_preview && $item['preview']) {
                        $item['preview'] = substr($item['preview'], 0, 110) . " ...";
                        echo "<div class=\"brz-eventList__item--meta--preview\">{$item['preview']}</div>";
                    }
                    if ($detail_url && $detail_page_button_text) {
                        echo "<div class=\"brz-ministryBrands__item--meta--button\"><a  href=\"{$detail_url}?ekklesia360_event_slug={$slug}\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">{$detail_page_button_text}</span></a></div>";
                    }
                    echo "</div>";
                }
                ?>
            </div>
            <?php
            if ($show_pagination && $content['after_show']['pagination']) {
                $content['after_show']['pagination'] = str_replace('id="pagination"', 'id="ekklesia360_event_list_pagination" class="brz-ministryBrands__pagination"', $content['after_show']['pagination']);
                $content['after_show']['pagination'] = str_replace('page=', 'ekklesia360_event_list_page=', $content['after_show']['pagination']);
                echo $content['after_show']['pagination'];
            }
        } //no output
        else {
            ?>

            <p>There are no events available.</p>

        <?php
        }
        ?>
<?php
    }
}
