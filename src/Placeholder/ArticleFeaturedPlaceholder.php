<?php

namespace BrizyEkklesia\Placeholder;

use BrizyEkklesia\HelperTrait;
use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class ArticleFeaturedPlaceholder extends PlaceholderAbstract
{
    use HelperTrait;

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
            'show_meta_icons'         => false,
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
                    echo "<div class=\"brz-ministryBrands__item--media\">";
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
            if ($show_title) {
                echo "<h2 class=\"brz-articleFeatured__item--meta--title brz-ministryBrands__item--meta-title\">";
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
                echo "<h6 class=\"brz-articleFeatured__item--meta brz-ministryBrands__item--meta-date\">";
                if ($show_meta_headings) {
                    if ($show_meta_icons) {
                        echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path fill=\"currentColor\" d=\"M96 32V64H48C21.5 64 0 85.5 0 112v48H448V112c0-26.5-21.5-48-48-48H352V32c0-17.7-14.3-32-32-32s-32 14.3-32 32V64H160V32c0-17.7-14.3-32-32-32S96 14.3 96 32zM448 192H0V464c0 26.5 21.5 48 48 48H400c26.5 0 48-21.5 48-48V192z\"></path></svg></span>";
                    } else {
                        echo "<span>Date: </span>";
                    }
                }
                echo "{$item['date']}";
                echo "</h6>";
            }
            if ($show_category && $item['category']) {
                echo "<h6 class=\"brz-articleFeatured__item--meta brz-ministryBrands__item--meta-category\">";
                if ($show_meta_headings) {
                    if ($show_meta_icons) {
                        echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 384 512\"><path fill=\"currentColor\" d=\"M0 48V487.7C0 501.1 10.9 512 24.3 512c5 0 9.9-1.5 14-4.4L192 400 345.7 507.6c4.1 2.9 9 4.4 14 4.4c13.4 0 24.3-10.9 24.3-24.3V48c0-26.5-21.5-48-48-48H48C21.5 0 0 21.5 0 48z\"></path></svg></span>";
                    } else {
                        echo "<span>Category: </span>";
                    }
                }
                echo "{$item['category']}";
                echo "</h6>";
            }
            if ($show_group && $item['group']) {
                echo "<h6 class=\"brz-articleFeatured__item--meta brz-ministryBrands__item--meta-group\">";
                if ($show_meta_headings) {
                    if ($show_meta_icons) {
                        echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 640 512\"><path fill=\"currentColor\" d=\"M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z\"></path></svg></span>";
                    } else {
                        echo "<span>Group: </span>";
                    }
                }
                echo "{$item['group']}";
                echo "</h6>";
            }
            if ($show_series && $item['series']) {
                echo "<h6 class=\"brz-articleFeatured__item--meta brz-ministryBrands__item--meta-series\">";
                if ($show_meta_headings) {
                    if ($show_meta_icons) {
                        echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\"><path fill=\"currentColor\" d=\"M40 48C26.7 48 16 58.7 16 72v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V72c0-13.3-10.7-24-24-24H40zM192 64c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zM16 232v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V232c0-13.3-10.7-24-24-24H40c-13.3 0-24 10.7-24 24zM40 368c-13.3 0-24 10.7-24 24v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V392c0-13.3-10.7-24-24-24H40z\"></path></svg></span>";
                    } else {
                        echo "<span>Series: </span>";
                    }
                }
                echo "{$item['series']}";
                echo "</h6>";
            }
            if ($show_author && $item['author']) {
                echo "<h6 class=\"brz-articleFeatured__item--meta brz-ministryBrands__item--meta-author\">";
                if ($show_meta_headings) {
                    if ($show_meta_icons) {
                        echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path fill=\"currentColor\" d=\"M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z\"></path></svg></span>";
                    } else {
                        echo "<span>Author: </span>";
                    }
                }
                echo "{$item['author']}";
                echo "</h6>";
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
                    echo "<li class=\"brz-ministryBrands__item--meta--links\"><a href=\"{$item['notes']}\" target=\"_blank\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 576 512\" class=\"brz-icon-svg align-[initial]\"><path d=\"M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM112 256H272c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H272c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H272c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16z\"></path></svg></a></li>";
                }
                echo "</ul>";
            }
            if ($show_preview && $item['preview']) {
                echo '<p class="brz-articleFeatured__item--meta--preview"><span>';
                echo $this->excerpt($item['preview']);
                echo '</span></p>';
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
