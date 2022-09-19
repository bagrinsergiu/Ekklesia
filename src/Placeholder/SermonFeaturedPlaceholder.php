<?php

namespace BrizyEkklesia\Placeholder\Ekklesia360;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class SermonFeaturedPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_sermon_featured';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'show_image'              => true,
            'show_video'              => true,
            'show_audio'              => true,
            'show_inline_video'       => true,
            'show_inline_audio'       => true,
            'show_title'              => true,
            'show_date'               => true,
            'show_category'           => true,
            'show_group'              => true,
            'show_series'             => true,
            'show_preacher'           => true,
            'show_passage'            => true,
            'show_meta_headings'      => true,
            'show_content'            => true,
            'sermon_latest'           => true,
            'sermon_recent_list'      => 'none',
            'sermon_slug'             => false,
            'category'                => 'all',
            'group'                   => 'all',
            'series'                  => 'all',
            'features'                => '',
            'nonfeatures'             => '',
            'show_media_links'        => true,
            'show_preview'            => true,
            'detail_page_button_text' => false,
            'detail_page'             => false,
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $cms                = $this->monkCMS;
        $sermon_recent_list = $settings['sermon_recent_list'] != 'none' ? $settings['sermon_recent_list'] : '';
        $category           = $settings['category'] != 'all' ? $settings['category'] : '';
        $group              = $settings['group'] != 'all' ? $settings['group'] : '';
        $series             = $settings['series'] != 'all' ? $settings['series'] : '';
        $detail_url         = $settings['detail_page'] ? home_url($settings['detail_page']) : false;
        $slug               = false;

        if ($features) {
            $nonfeatures = '';
        } elseif ($nonfeatures) {
            $features = '';
        }

        if ($sermon_latest != '') {
            $content = $cms->get([
                'module'        => 'sermon',
                'display'       => 'list',
                'order'         => 'recent',
                'howmany'       => 1,
                'find_category' => $category,
                'find_group'    => $group,
                'find_series'   => $series,
                'features'      => $features,
                'nonfeatures'   => $nonfeatures,
                'emailencode'   => 'no',
                'show'          => "__videoplayer fullscreen='true'__",
                'show'          => "__audioplayer__",
            ])['show'][0];

        } else {

            if ($sermon_slug) {
                $slug = $sermon_slug;
            } elseif ($sermon_recent_list != '') {
                $slug = $sermon_recent_list;
            }
        }

        if ($slug) {
            $content = $cms->get([
                'module'      => 'sermon',
                'display'     => 'detail',
                'find'        => $slug,
                'emailencode' => 'no',
                'show'        => "__videoplayer fullscreen='true'__",
                'show'        => "__audioplayer__",
            ])['show'];
        }
        ?>

        <div class="ekklesia360_sermon_featured_wrap">

            <?php //output
            if (count($content) > 0) {
                $item = $content;
                ?>

                <div class="ekklesia360_sermon_featured">
                    <?php
                    echo "<article>";
                    echo "<div class=\"info\">";
                    if ($show_title) {

                        echo "<h2 class=\"ekklesia360_sermon_featured_heading\">";
                        if ($detail_url) echo "<a href=\"{$detail_url}?ekklesia360_sermon_slug={$item['slug']}\">";
                        echo "{$item['title']}";
                        if ($detail_url) echo "</a>";
                        echo "</h2>";
                    }
                    if ($show_date && $item['date']) {
                        echo "<h6 class=\"ekklesia360_sermon_featured_meta\">";
                        if ($show_meta_headings) echo "Date: ";
                        echo "{$item['date']}";
                        echo "</h6>";
                    }
                    if ($show_category && $item['category']) {
                        echo "<h6 class=\"ekklesia360_sermon_featured_meta\">";
                        if ($show_meta_headings) echo "Category: ";
                        echo "{$item['category']}";
                        echo "</h6>";
                    }
                    if ($show_group && $item['group']) {
                        echo "<h6 class=\"ekklesia360_sermon_featured_meta\">";
                        if ($show_meta_headings) echo "Group: ";
                        echo "{$item['group']}";
                        echo "</h6>";
                    }
                    if ($show_series && $item['series']) {
                        echo "<h6 class=\"ekklesia360_sermon_featured_meta\">";
                        if ($show_meta_headings) echo "Series: ";
                        echo "{$item['series']}";
                        echo "</h6>";
                    }
                    if ($show_preacher && $item['preacher']) {
                        echo "<h6 class=\"ekklesia360_sermon_featured_meta\">";
                        if ($show_meta_headings) echo "Speaker: ";
                        echo "{$item['preacher']}";
                        echo "</h6>";
                    }
                    if ($show_passage && $item['passages']) {
                        echo "<h6 class=\"ekklesia360_sermon_featured_meta\">";
                        if ($show_meta_headings) echo "Passages: ";
                        echo "{$item['passages']}";
                        echo "</h6>";
                    }
                    if ($show_image && $item['imageurl'] && !$show_video) {
                        echo "<div class=\"image\">";
                        if ($detail_url) echo "<a href=\"{$detail_url}?ekklesia360_sermon_slug={$item['slug']}\">";
                        echo "<img src=\"{$item['imageurl']}\" alt=\"\" />";
                        if ($detail_url) echo "</a>";
                        echo "</div>";
                    }
                    if ($show_video) {
                        if ($item['videoembed']) {
                            echo "<div class=\"ekklesia360_sermon_media_responsive video\">{$item['videoembed']}</div>";
                        } elseif ($item['videourl']) {
                            $videoext = pathinfo($item['videourl'], PATHINFO_EXTENSION);
                            echo "<div class=\"ekklesia360_sermon_media_responsive\">";
                            echo "<video src=\"{$item['videourl']}\" controls preload=\"none\" width=\"1024\" height=\"576\" poster=\"{$item['imageurl']}\" type=\"video/{$videoext}\"><p>The Video could not be played. Please <a href=\"{$item['videourl']}\" target=\"_blank\">download it here</a>.</p></video>";
                            echo "</div>";
                        } elseif ($show_image && $item['imageurl']) {
                            echo "<div class=\"image\">";
                            if ($detail_url) echo "<a href=\"{$detail_url}?ekklesia360_sermon_slug={$item['slug']}\">";
                            echo "<img src=\"{$item['imageurl']}\" alt=\"\" />";
                            if ($detail_url) echo "</a>";
                            echo "</div>";
                        }
                    }
                    if ($show_audio && $item['audiourl']) {
                        echo "<div class=\"ekklesia360_sermon_media_audio\">";
                        echo "<audio src=\"{$item['audiourl']}\" controls preload=\"none\"></audio>";
                        echo "</div>";
                    }
                    if ($show_media_links) {
                        echo "<ul class=\"ekklesia360_sermon_featured_media\">";
                        if ($item['videoplayer']) {
                            $item['videoplayer'] = preg_replace('/<a(.+?)>.+?<\/a>/i', "<a$1><i class=\"fas fa-desktop\"></i></a>", $item['videoplayer']);
                            echo "<li class=\"ekklesia360_sermon_featured_media_videoplayer\">{$item['videoplayer']}</li>";
                        }
                        if ($item['audioplayer']) {
                            $item['audioplayer'] = preg_replace('/<a(.+?)>.+?<\/a>/i', "<a$1><i class=\"fas fa-volume-up\"></i></a>", $item['audioplayer']);
                            echo "<li class=\"ekklesia360_sermon_featured_media_audioplayer\">{$item['audioplayer']}</li>";
                        }
                        if ($item['notes']) {
                            echo "<li class=\"ekklesia360_sermon_featured_media_notes\"><a href=\"{$item['notes']}\" target=\"_blank\"><i class=\"fas fa-file-alt\"></i></a></li>";
                        }
                        echo "</ul>";
                    }
                    if ($show_preview && $item['preview']) {
                        $item['preview'] = substr($item['preview'], 0, 110) . " ...";
                        echo "<p class=\"ekklesia360_sermon_featured_preview\">{$item['preview']}</p>";
                    }
                    if ($show_content && $item['text']) {
                        echo "<div class=\"ekklesia360_sermon_featured_content\">{$item['text']}</div>";
                    }

                    if ($detail_url && $detail_page_button_text) {
                        echo "<p class=\"ekklesia360_sermon_featured_detail_button\"><a href=\"{$detail_url}?ekklesia360_sermon_slug={$item['slug']}\" class=\"elementor-button-link elementor-button elementor-size-sm\"><span class=\"elementor-button-text\">{$detail_page_button_text}</span></a></p>";
                    }

                    echo "</div>";
                    echo "</article>";
                    ?>
                </div>
                <?php
            } //no output
            else {
                ?>

                <p>There is no sermon available.</p>

                <?php
            }
            ?>
        </div>
        <?php
    }
}