<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class ArticleFeaturedPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_article_featured';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $settings = array_merge([
            'show_image'              => true,
            'show_video'              => false,
            'show_audio'              => false,
            'show_media_links'        => false,
            'show_title'              => true,
            'show_date'               => true,
            'show_category'           => true,
            'show_group'              => false,
            'show_series'             => false,
            'show_author'             => true,
            'show_meta_headings'      => false,
            'show_preview'            => false,
            'show_content'            => false,
            'detail_page_button_text' => '',
            'detail_page'             => '',
            'article_slug'            => '',
            'recentArticles'          => '',
            'features'                => '',
            'nonfeatures'             => '',
            'series'                  => 'all',
            'category'                => 'all',
            'group'                   => 'all',
            'show_latest_articles'    => true,
        ], $placeholder->getAttributes());

        extract($settings);

        $detail_url = $detail_page ? $this->replacer->replacePlaceholders(urldecode($detail_page), $context) : false;

        if ($show_latest_articles) {
            $content = $this->monkCMS->get([
                'module'        => 'article',
                'display'       => 'list',
                'order'         => 'recent',
                'howmany'       => 1,
                'find_category' => $settings['category'] != 'all' ? $settings['category'] : '',
                'find_group'    => $settings['group'] != 'all' ? $settings['group'] : '',
                'find_series'   => $settings['series'] != 'all' ? $settings['series'] : '',
                'features'      => $nonfeatures ? '' : $features,
                'nonfeatures'   => $features ? '' : $nonfeatures,
                'emailencode'   => 'no',
            ]);

            $item = empty($content['show'][0]) ? [] : $content['show'][0];

        } else {

            $item = $this->monkCMS->get([
                'module'      => 'article',
                'display'     => 'detail',
                'find'        => $recentArticles ?: $article_slug,
                'emailencode' => 'no',
            ]);

            $item = empty($item['show']) ? [] : $item['show'];
        }

        if ($item) {
            echo "<article>";
            echo "<div class=\"brz-articleFeatured__item\">";
            if ($show_title) {
                echo "<h2 class=\"brz-articleFeatured__item--meta--title\">";
                if ($detail_url) {
                    echo "<a href=\"{$detail_url}?mc-slug={$item['slug']}\">";
                }
                echo "{$item['title']}";
                if ($detail_url) {
                    echo "</a>";
                }
                echo "</h2>";
            }
            if ($show_date && $item['date']) {
                echo "<h6 class=\"brz-articleFeatured__item--meta\">";
                if ($show_meta_headings) {
                    echo "Date: ";
                }
                echo "{$item['date']}";
                echo "</h6>";
            }
            if ($show_category && $item['category']) {
                echo "<h6 class=\"brz-articleFeatured__item--meta\">";
                if ($show_meta_headings) {
                    echo "Category: ";
                }
                echo "{$item['category']}";
                echo "</h6>";
            }
            if ($show_group && $item['group']) {
                echo "<h6 class=\"brz-articleFeatured__item--meta\">";
                if ($show_meta_headings) {
                    echo "Group: ";
                }
                echo "{$item['group']}";
                echo "</h6>";
            }
            if ($show_series && $item['series']) {
                echo "<h6 class=\"brz-articleFeatured__item--meta\">";
                if ($show_meta_headings) {
                    echo "Series: ";
                }
                echo "{$item['series']}";
                echo "</h6>";
            }
            if ($show_author && $item['author']) {
                echo "<h6 class=\"brz-articleFeatured__item--meta\">";
                if ($show_meta_headings) {
                    echo "Author: ";
                }
                echo "{$item['author']}";
                echo "</h6>";
            }
            if ($show_image && $item['imageurl'] && !$show_video) {
                echo "<div class=\"brz-ministryBrands__item--media\">";
                if ($detail_url) {
                    echo "<a href=\"{$detail_url}?mc-slug={$item['slug']}\">";
                }
                echo "<img src=\"{$item['imageurl']}\" alt=\"\" />";
                if ($detail_url) {
                    echo "</a>";
                }
                echo "</div>";
            }
            if ($show_video) {
                if ($item['videoembed']) {
                    echo "<div class=\"brz-ministryBrands__item--media\">{$item['videoembed']}</div>";
                } elseif ($item['videourl']) {
                    $videoext = pathinfo($item['videourl'], PATHINFO_EXTENSION);
                    echo "<div class=\"brz-ministryBrands__item--media\">";
                    echo "<video src=\"{$item['videourl']}\" controls preload=\"none\" width=\"1024\" height=\"576\" poster=\"{$item['imageurl']}\" type=\"video/{$videoext}\"><p>The Video could not be played. Please <a href=\"{$item['videourl']}\" target=\"_blank\">download it here</a>.</p></video>";
                    echo "</div>";
                } elseif ($show_image && $item['imageurl']) {
                    echo "<div class=\"image\">";
                    if ($detail_url) {
                        echo "<a href=\"{$detail_url}?nc-slug={$item['slug']}\">";
                    }
                    echo "<img src=\"{$item['imageurl']}\" alt=\"\" />";
                    if ($detail_url) {
                        echo "</a>";
                    }
                    echo "</div>";
                }
            }
            if ($show_audio && $item['audiourl']) {
                echo "<div class=\"brz-articleFeatured__item--media\">";
                echo "<audio src=\"{$item['audiourl']}\" controls preload=\"none\"></audio>";
                echo "</div>";
            }
            if ($show_media_links) {
                echo "<ul class=\"brz-articleFeatured__item--media--links\">";
                if ($item['videoplayer']) {
                    $item['videoplayer'] = preg_replace(
                        '/<a(.+?)>.+?<\/a>/i',
                        '<a$1><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="brz-icon-svg align-[initial]"><path d="M528 0H48C21.5 0 0 21.5 0 48v320c0 26.5 21.5 48 48 48h192l-16 48h-72c-13.3 0-24 10.7-24 24s10.7 24 24 24h272c13.3 0 24-10.7 24-24s-10.7-24-24-24h-72l-16-48h192c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48zm-16 352H64V64h448v288z"></path></svg></a>',
                        $item['videoplayer']
                    );
                    echo "<li class=\"brz-ministryBrands__item--meta--links\">{$item['videoplayer']}</li>";
                }
                if ($item['audioplayer']) {
                    $item['audioplayer'] = preg_replace(
                        '/<a(.+?)>.+?<\/a>/i',
                        '<a$1><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="brz-icon-svg align-[initial]"><path d="M215.03 71.05L126.06 160H24c-13.26 0-24 10.74-24 24v144c0 13.25 10.74 24 24 24h102.06l88.97 88.95c15.03 15.03 40.97 4.47 40.97-16.97V88.02c0-21.46-25.96-31.98-40.97-16.97zm233.32-51.08c-11.17-7.33-26.18-4.24-33.51 6.95-7.34 11.17-4.22 26.18 6.95 33.51 66.27 43.49 105.82 116.6 105.82 195.58 0 78.98-39.55 152.09-105.82 195.58-11.17 7.32-14.29 22.34-6.95 33.5 7.04 10.71 21.93 14.56 33.51 6.95C528.27 439.58 576 351.33 576 256S528.27 72.43 448.35 19.97zM480 256c0-63.53-32.06-121.94-85.77-156.24-11.19-7.14-26.03-3.82-33.12 7.46s-3.78 26.21 7.41 33.36C408.27 165.97 432 209.11 432 256s-23.73 90.03-63.48 115.42c-11.19 7.14-14.5 22.07-7.41 33.36 6.51 10.36 21.12 15.14 33.12 7.46C447.94 377.94 480 319.54 480 256zm-141.77-76.87c-11.58-6.33-26.19-2.16-32.61 9.45-6.39 11.61-2.16 26.2 9.45 32.61C327.98 228.28 336 241.63 336 256c0 14.38-8.02 27.72-20.92 34.81-11.61 6.41-15.84 21-9.45 32.61 6.43 11.66 21.05 15.8 32.61 9.45 28.23-15.55 45.77-45 45.77-76.88s-17.54-61.32-45.78-76.86z"></path></svg></a>',
                        $item['audioplayer']
                    );
                    echo "<li class=\"brz-ministryBrands__item--meta--links\">{$item['audioplayer']}</li>";
                }
                if ($item['notes']) {
                    echo "<li class=\"brz-ministryBrands__item--meta--links\"><a href=\"{$item['notes']}\" target=\"_blank\"><i class=\"fas fa-file-alt\"></i></a></li>";
                }
                echo "</ul>";
            }
            if ($show_preview && $item['preview']) {
                if (strlen($item['preview']) >= 110) {
                    $item['preview'] = substr($item['preview'], 0, 110) . "...";
                }
                echo "<p class=\"brz-articleFeatured__item--meta--preview\"><span>{$item['preview']}</span></p>";
            }
            if ($show_content && $item['text']) {
                echo "<div class=\"brz-articleFeatured__content\">{$item['text']}</div>";
            }
            if ($detail_url && $detail_page_button_text) {
                echo "<p class=\"brz-ministryBrands__item--meta--button\"><a href=\"{$detail_url}?mc-slug={$item['slug']}\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">{$detail_page_button_text}</span></a></p>";
            }
            echo "</div>";
            echo "</article>";

        } else {
            echo '<p>There is no article available.</p>';
        }
    }
}
