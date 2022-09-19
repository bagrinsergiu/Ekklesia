<?php

namespace BrizyEkklesia\Placeholder\Ekklesia360;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class EventListPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_event_list';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'show_images'             => true,
            'show_title'              => true,
            'show_date'               => true,
            'show_category'           => true,
            'show_group'              => true,
            'show_meta_headings'      => true,
            'category'                => 'all',
            'group'                   => 'all',
            'features'                => '',
            'nonfeatures'             => '',
            'show_preview'            => true,
            'detail_page_button_text' => false,
            'detail_page'             => false,
            'howmany'                 => 9,
            'column_count'            => 3,
            'column_count_tablet'     => 2,
            'column_count_mobile'     => 1,
            'show_pagination'         => true,
            'show_location'           => true,
            'show_registration'       => true,
            'sticky_space'            => 0,
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $cms        = $this->monkCMS;
        $category   = $settings['category'] != 'all' ? $settings['category'] : '';
        $group      = $settings['group'] != 'all' ? $settings['group'] : '';
        $detail_url = $settings['detail_page'] ? home_url($settings['detail_page']) : false;
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

        <div id="ekklesia360_event_list_wrap" class="ekklesia360_event_list_wrap">

            <?php //output
            if (count($content['show']) > 0) {
                ?>

                <div class="ekklesia360_event_list" data-columncount="<?php echo $column_count; ?>"
                     data-columncount-tablet="<?php echo $column_count_tablet; ?>"
                     data-columncount-mobile="<?php echo $column_count_mobile; ?>">
                    <?php
                    foreach ($content['show'] as $key => $item) {
                        //__id__-__eventstart format='Y-m-d'__-__slug__
                        $slugDate = date("Y-m-d", strtotime($item["eventstart"]));
                        $slug = "{$item['id']}-$slugDate-{$item['slug']}";

                        echo "<article>";
                        echo "<div class=\"info\">";
                        if ($show_images && $item['imageurl']) {
                            if ($detail_url) echo "<a href=\"{$detail_url}?ekklesia360_event_slug={$slug}\">";
                            echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                            if ($detail_url) echo "</a>";
                        }

                        if ($show_title) {
                            echo "<h4 class=\"ekklesia360_event_list_heading\">";
                            if ($detail_url) echo "<a href=\"{$detail_url}?ekklesia360_event_slug={$slug}\">";
                            echo "{$item['title']}";
                            if ($detail_url) echo "</a>";
                            echo "</h4>";
                        }

                        if ($show_date) {
                            echo "<h5 class=\"ekklesia360_event_list_times\">Date: {$item['eventtimes']}</h5>";
                        }

                        if ($show_category && $item['category']) {
                            echo "<h6 class=\"ekklesia360_event_list_meta\">";
                            if ($show_meta_headings) echo "Category: ";
                            echo "{$item['category']}";
                            echo "</h6>";
                        }
                        if ($show_group && $item['group']) {
                            echo "<h6 class=\"ekklesia360_event_list_meta\">";
                            if ($show_meta_headings) echo "Group: ";
                            echo "{$item['group']}";
                            echo "</h6>";
                        }
                        if ($show_location && $item['location']) {
                            echo "<h6 class=\"ekklesia360_event_list_meta\">";
                            if ($show_meta_headings) echo "Location: ";
                            echo "{$item['location']}";
                            echo "</h6>";
                            if ($item['fulladdress']) {
                                echo "<h6 class=\"ekklesia360_event_list_meta\">";
                                if ($show_meta_headings) echo "Address: ";
                                echo "<a href=\"http://maps.google.com/maps?q={$item["fulladdress"]}\" target=\"_blank\">{$item['fulladdress']}</a>";
                                echo "</h6>";
                            }
                        }
                        if ($show_registration && ($item['registrationurl'] || $item['externalregistrationurl'])) {
                            if ($item['registrationurl']) {
                                echo "<p class=\"ekklesia360_event_list_meta\"><a href=\"{$item['registrationurl']}\" target=\"_blank\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">Register</span></a></p>";
                            }
                            if ($item['externalregistrationurl']) {
                                echo "<p class=\"ekklesia360_event_list_meta\"><a href=\"{$item['externalregistrationurl']}\" target=\"_blank\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">Register</span></a></p>";
                            }
                        }
                        if ($show_preview && $item['preview']) {
                            $item['preview'] = substr($item['preview'], 0, 110) . " ...";
                            echo "<p class=\"ekklesia360_event_list_preview\">{$item['preview']}</p>";
                        }
                        if ($detail_url && $detail_page_button_text) {
                            echo "<p class=\"ekklesia360_event_list_detail_button\"><a href=\"{$detail_url}?ekklesia360_event_slug={$slug}\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">{$detail_page_button_text}</span></a></p>";
                        }
                        echo "</div>";
                        echo "</article>";
                    }
                    ?>
                </div>
                <?php
                if ($show_pagination && $content['after_show']['pagination']) {
                    $content['after_show']['pagination'] = str_replace('id="pagination"', 'id="ekklesia360_event_list_pagination" class="ekklesia360_pagination"', $content['after_show']['pagination']);
                    $content['after_show']['pagination'] = str_replace('page=', 'ekklesia360_event_list_page=', $content['after_show']['pagination']);
                    echo $content['after_show']['pagination'];
                }
                if (count($_GET)) {
                    echo "<script>";
                    echo "const id = 'ekklesia360_event_list_wrap';";
                    echo "const yOffset = -" . $sticky_space . ";";
                    echo "const element = document.getElementById(id);";
                    echo "const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;";
                    echo "window.scrollTo({top: y, behavior: 'smooth'});";
                    echo "</script>";
                }
            } //no output
            else {
                ?>

                <p>There are no events available.</p>

                <?php
            }
            ?>
        </div>
        <?php
    }
}