<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class SermonFeaturedPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_sermon_featured';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'show_image'              => false,
            'show_video'              => false,
            'show_audio'              => false,
            'show_inline_video'       => false,
            'show_inline_audio'       => false,
            'show_title'              => false,
            'show_date'               => false,
            'show_category'           => false,
            'show_group'              => false,
            'show_series'             => false,
            'show_preacher'           => false,
            'show_passage'            => false,
            'show_meta_headings'      => false,
            'show_content'            => false,
            'sermon_latest'           => false,
            'sermon_recent_list'      => 'none',
            'sermon_slug'             => false,
            'category'                => 'all',
            'group'                   => 'all',
            'series'                  => 'all',
            'features'                => '',
            'nonfeatures'             => '',
            'show_media_links'        => false,
            'show_preview'            => false,
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


        <?php //output
        if (count($content) > 0) {
            $item = $content;
        ?>

            <div class="brz-sermonFeatured__container">
                <?php
                echo "<div class=\"brz-sermonFeatured__item\">";
                if ($show_title) {

                    echo "<h2 class=\"brz-sermonFeatured__item--meta--title\">";
                    if ($detail_url) echo "<a href=\"{$detail_url}?ekklesia360_sermon_slug={$item['slug']}\">";
                    echo "{$item['title']}";
                    if ($detail_url) echo "</a>";
                    echo "</h2>";
                }
                if ($show_date && $item['date']) {
                    echo "<h6 class=\"brz-sermonFeatured__item--meta\">";
                    if ($show_meta_headings) echo "Date: ";
                    echo "{$item['date']}";
                    echo "</h6>";
                }
                if ($show_category && $item['category']) {
                    echo "<h6 class=\"brz-sermonFeatured__item--meta\">";
                    if ($show_meta_headings) echo "Category: ";
                    echo "{$item['category']}";
                    echo "</h6>";
                }
                if ($show_group && $item['group']) {
                    echo "<h6 class=\"brz-sermonFeatured__item--meta\">";
                    if ($show_meta_headings) echo "Group: ";
                    echo "{$item['group']}";
                    echo "</h6>";
                }
                if ($show_series && $item['series']) {
                    echo "<h6 class=\"brz-sermonFeatured__item--meta\">";
                    if ($show_meta_headings) echo "Series: ";
                    echo "{$item['series']}";
                    echo "</h6>";
                }
                if ($show_preacher && $item['preacher']) {
                    echo "<h6 class=\"brz-sermonFeatured__item--meta\">";
                    if ($show_meta_headings) echo "Speaker: ";
                    echo "{$item['preacher']}";
                    echo "</h6>";
                }
                if ($show_passage && $item['passages']) {
                    echo "<h6 class=\"brz-sermonFeatured__item--meta--passages\">";
                    if ($show_meta_headings) echo "<span class='brz-sermonFeatured__item--meta'>Passages: </span>";
                    echo "<span class='brz-ministryBrands__item--meta--links'>";
                    echo $item['passages'];
                    echo "</span>";
                    echo "</h6>";
                }
                if ($show_image && $item['imageurl'] && !$show_video) {
                    echo "<div class=\"image\">";
                    if ($detail_url) echo "<a class='brz-ministryBrands__item--meta--links' href=\"{$detail_url}?ekklesia360_sermon_slug={$item['slug']}\">";
                    echo "<img src=\"{$item['imageurl']}\" alt=\"\" />";
                    if ($detail_url) echo "</a>";
                    echo "</div>";
                }
                if ($show_video) {
                    if ($item['videoembed']) {
                        echo "<div class=\"brz-sermonFeatured__item--media--container\">{$item['videoembed']}</div>";
                    } elseif ($item['videourl']) {
                        $videoext = pathinfo($item['videourl'], PATHINFO_EXTENSION);
                        echo "<div class=\"brz-sermonFeatured__item--media\">";
                        echo "<video src=\"{$item['videourl']}\" controls preload=\"none\" width=\"1024\" height=\"576\" poster=\"{$item['imageurl']}\" type=\"video/{$videoext}\"><p>The Video could not be played. Please <a href=\"{$item['videourl']}\" target=\"_blank\">download it here</a>.</p></video>";
                        echo "</div>";
                    } elseif ($show_image && $item['imageurl']) {
                        echo "<div class=\"image\">";
                        if ($detail_url) echo "<a class='brz-ministryBrands__item--meta--links' href=\"{$detail_url}?ekklesia360_sermon_slug={$item['slug']}\">";
                        echo "<img src=\"{$item['imageurl']}\" alt=\"\" />";
                        if ($detail_url) echo "</a>";
                        echo "</div>";
                    }
                }
                if ($show_audio && $item['audiourl']) {
                    echo "<div class=\"brz-sermonFeatured__item--media\">";
                    echo "<audio src=\"{$item['audiourl']}\" controls preload=\"none\"></audio>";
                    echo "</div>";
                }
                if ($show_media_links) {
                    echo "<ul class=\"brz-sermonFeatured__item--media--links\">";
                    if ($item['videoplayer']) {
                        $item['videoplayer'] = preg_replace('/<a(.+?)>.+?<\/a>/i', "<a$1><i class=\"fas fa-desktop\"></i></a>", $item['videoplayer']);
                        echo "<li class=\"brz-ministryBrands__item--meta--links\">{$item['videoplayer']}</li>";
                    }
                    if ($item['audioplayer']) {
                        $item['audioplayer'] = preg_replace('/<a(.+?)>.+?<\/a>/i', "<a$1><i class=\"fas fa-volume-up\"></i></a>", $item['audioplayer']);
                        echo "<li class=\"brz-ministryBrands__item--meta--links\">{$item['audioplayer']}</li>";
                    }
                    if ($item['notes']) {
                        echo "<li class=\"brz-ministryBrands__item--meta--links\"><a href=\"{$item['notes']}\" target=\"_blank\"><i class=\"fas fa-file-alt\"></i></a></li>";
                    }
                    echo "</ul>";
                }
                if ($show_preview && $item['preview']) {
                    $item['preview'] = substr($item['preview'], 0, 110) . " ...";
                    echo "<p class=\"brz-sermonFeatured__item--meta--preview\">{$item['preview']}</p>";
                }
                if ($show_content && $item['text']) {
                    echo "<div class=\"brz-sermonFeatured__item--meta--preview\">{$item['text']}</div>";
                }

                if ($detail_url && $detail_page_button_text) {
                    echo "<p class=\"brz-ministryBrands__item--meta--links\"><a href=\"{$detail_url}?ekklesia360_sermon_slug={$item['slug']}\" class=\"brz-ministryBrands__item--meta--links\"><span class=\"brz-button-text\">{$detail_page_button_text}</span></a></p>";
                }

                echo "</div>";
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
<?php
    }
}
