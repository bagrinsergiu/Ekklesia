<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class GroupSliderPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_group_slider';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'show_image'                      => false,
            'show_category'                   => false,
            'show_group'                      => false,
            'category'                        => 'all',
            'group'                           => 'all',
            'show_preview'                    => false,
            'detail_page_button_text'         => false,
            'detail_page'                     => false,
            'howmany'                         => 9,
            'column_count'                    => 3,
            'show_pagination'                 => false,
            'show_images'                     => false,
            'show_day'                        => false,
            'show_times'                      => false,
            'show_status'                     => false,
            'show_childcare'                  => false,
            'show_resourcelink'               => false,
            'howmany_show'                    => 3,
            'show_arrows'                     => false
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $cms        = $this->monkCMS;
        $detail_url = $settings['detail_page'] ? home_url($settings['detail_page']) : false;

        $content = $cms->get([
            'module'        => 'smallgroup',
            'display'       => 'list',
            'order'         => 'recent',
            'emailencode'   => 'no',
            'howmany'       => $howmany,
            'page'          => isset($_GET['brz-groupSlider_page']) ? $_GET['ekklesia360_group_list_page'] : 1,
            'find_category' => $category == 'all' ? '' : $category,
            'find_group'    => $group == 'all' ? '' : $group,
            'show'          => "__starttime format='g:ia'__",
            'show'          => "__endtime format='g:ia'__"
        ]);
        ?>

        <div class="brz-groupSlider_wrap" data-showarrows="<?= $show_arrows ?>"
             data-showpagination="<?= $show_pagination ?>">

            <?php //output
            if (!empty($content['show'])) {
                ?>

                <div class="brz-groupSlider-swiper-container" data-howmanyshow="<?= $howmany_show ?>">
                    <div class="brz-groupSlider-swiper-wrapper" data-show="<?= $column_count?>">
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

                            echo "<article class=\"brz-groupSlider-swiper-slide\">";
                            echo "<div class=\"brz-groupSlider-info\">";
                            if ($show_images && $item['imageurl']) {
                                if ($detail_url) echo "<a href=\"{$detail_url}?ekklesia360_group_slug={$item['slug']}\">";
                                echo "<div class=\"brz-groupSlider-image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                                if ($detail_url) echo "</a>";
                            }

                            echo "<h4 class=\"brz-groupSlider_heading\">";
                            if ($detail_url) echo "<a href=\"{$detail_url}?ekklesia360_group_slug={$item['slug']}\">";
                            echo "{$item['name']}";
                            if ($detail_url) echo "</a>";
                            echo "</h4>";

                            if ($show_day && $item['dayoftheweek']) {
                                echo "<h6 class=\"brz-groupSlider_meta\">Meeting Day: {$item['dayoftheweek']}</h6>";
                            }
                            if ($show_times && ($item['starttime'] || $item['endtime'])) {
                                echo "<h6 class=\"brz-groupSlider_meta\">Meeting Times: ";
                                if ($item['starttime']) echo "{$item['starttime']}";
                                if ($item['endtime']) echo " - {$item['endtime']}";
                                echo "</h6>";
                            }
                            if ($show_category && $item['category']) {
                                echo "<h6 class=\"brz-groupSlider_meta\">Category: {$item['category']}</h6>";
                            }
                            if ($show_group && $item['group']) {
                                echo "<h6 class=\"brz-groupSlider_meta\">Group: {$item['group']}</h6>";
                            }
                            if ($show_status && $item['groupstatus']) {
                                echo "<h6 class=\"brz-groupSlider_meta\">Status: {$item['groupstatus']}</h6>";
                            }
                            if ($show_childcare) {
                                $childcare = "No";
                                if ($item['childcareprovided']) {
                                    $childcare = "Yes";
                                }
                                echo "<h6 class=\"brz-groupSlider_meta\">Childcare Provided: {$childcare}</h6>";
                            }
                            if ($show_resourcelink && $item['resourcelink']) {
                                $resource_target = "";
                                if ($item['iflinknewwindow']) {
                                    $resource_target = " target=\"_blank\"";
                                }
                                echo "<h6 class=\"brz-groupSlider_meta\">Resource: <a href=\"{$item['resourcelink']}\" {$resource_target}>{$item['resourcelink']}</a></h6>";
                            }
                            if ($show_preview && $item['description']) {
                                $item['description'] = substr($item['description'], 0, 110) . " ...";
                                echo "<p class=\"brz-groupSlider_preview\">{$item['description']}</p>";
                            }
                            if ($detail_url && $detail_page_button_text) {
                                echo "<p class=\"brz-groupSlider_detail_button\"><a href=\"{$detail_url}?ekklesia360_group_slug={$item['slug']}\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">{$detail_page_button_text}</span></a></p>";
                            }
                            echo "</div>";
                            echo "</article>";
        
                        }
                        ?>
                    </div>
                    <?php if ($show_pagination): ?>
                        <div class="brz-groupSlider-swiper-pagination"></div>
                    <?php endif; ?>
                </div>
                <?php if ($show_arrows): ?>
                    <div class="brz-swiper-arrow brz-swiper-arrow_prev"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" class="brz-icon-svg align-[initial]" data-type="fa" data-name="angle-left"><path d="M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z"></path></svg></i></div>
                    <div class="brz-swiper-arrow brz-swiper-arrow_next"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" class="brz-icon-svg align-[initial]" data-type="fa" data-name="angle-right"><path d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg>
                </div>
                <?php endif; ?>
                <?php
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