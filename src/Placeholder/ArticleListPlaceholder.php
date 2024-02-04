<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class ArticleListPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_article_list';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'category'                  => '',
            'group'                     => '',
            'series'                    => '',
            'howmany'                   => 3,
            'show_pagination'           => false,
            'features'                  => '', # empty/features
            'nonfeatures'               => '', # empty/nonfeatures
            'show_images'               => true,
            'show_video'                => false,
            'show_audio'                => false,
            'show_media_links'          => false,
            'show_title'                => true,
            'show_date'                 => false,
            'show_category'             => false,
            'show_group'                => false,
            'show_series'               => false,
            'show_author'               => false,
            'show_meta_headings'        => false,
            'show_preview'              => false,
            'detail_page_button_text'   => '',
            'detail_page'               => '',
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $detail_url  = $detail_page ? $this->replacer->replacePlaceholders(urldecode($detail_page), $context) : '';
        $content     = $this->monkCMS->get([
            'module'        => 'article',
            'display'       => 'list',
            'order'         => 'recent',
            'emailencode'   => 'no',
            'howmany'       => $howmany,
            'page'          => isset($_GET['mc-page']) ? $_GET['mc-page'] : 1,
            'find_category' => $category != 'all' ? $category : '',
            'find_group'    => $group != 'all' ? $group : '',
            'find_series'   => $series != 'all' ? $series : '',
            'features'      => $nonfeatures ? '' : $features,
            'nonfeatures'   => $features ? '' : $nonfeatures,
            'after_show'    => '__pagination__',
        ]);
        ?>

        <div id="ekklesia360_article_list_wrap" class="ekklesia360_article_list_wrap">

            <?php if (!empty($content['show'])) : ?>

                <div class="ekklesia360_article_list">
                    <?php
                    foreach ($content['show'] as $item) {
                        echo "<article>";
                        echo "<div class=\"info\">";

                        if ($show_images && $item['imageurl'] && !$show_video) {
                            if ($detail_url) {
                                echo "<a href=\"{$detail_url}?mc-slug={$item['slug']}\">";
                            }
                            echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                            if ($detail_url) {
                                echo "</a>";
                            }
                        }
                        if ($show_video) {
                            if ($item['videoembed']) {
                                echo "<div class=\"ekklesia360_article_media_responsive video\">{$item['videoembed']}</div>";
                            } elseif ($item['videourl']) {
                                $videoext = pathinfo($item['videourl'], PATHINFO_EXTENSION);
                                echo "<div class=\"ekklesia360_article_media_responsive\">";
                                echo "<video src=\"{$item['videourl']}\" controls preload=\"none\" width=\"1024\" height=\"576\" poster=\"{$item['imageurl']}\" type=\"video/{$videoext}\"><p>The Video could not be played. Please <a href=\"{$item['videourl']}\" target=\"_blank\">download it here</a>.</p></video>";
                                echo "</div>";
                            } elseif ($show_images && $item['imageurl']) {
                                echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                            }
                        }
                        if ($show_audio && $item['audiourl']) {
                            echo "<div class=\"ekklesia360_article_media_audio\">";
                            echo "<audio src=\"{$item['audiourl']}\" controls preload=\"none\"></audio>";
                            echo "</div>";
                        }

                        if ($show_title) {
                            echo "<h4 class=\"ekklesia360_article_list_heading\">";
                            if ($detail_url) {
                                echo "<a href=\"{$detail_url}?mc-slug={$item['slug']}\">";
                            }
                            echo "{$item['title']}";
                            if ($detail_url) {
                                echo "</a>";
                            }
                            echo "</h4>";
                        }

                        if ($show_date && $item['date']) {
                            echo "<h6 class=\"ekklesia360_article_list_meta\">";
                            if ($show_meta_headings) {
                                echo "Date: ";
                            }
                            echo "{$item['date']}";
                            echo "</h6>";
                        }
                        if ($show_category && $item['category']) {
                            echo "<h6 class=\"ekklesia360_article_list_meta\">";
                            if ($show_meta_headings) {
                                echo "Category: ";
                            }
                            echo "{$item['category']}";
                            echo "</h6>";
                        }
                        if ($show_group && $item['group']) {
                            echo "<h6 class=\"ekklesia360_article_list_meta\">";
                            if ($show_meta_headings) {
                                echo "Group: ";
                            }
                            echo "{$item['group']}";
                            echo "</h6>";
                        }
                        if ($show_series && $item['series']) {
                            echo "<h6 class=\"ekklesia360_article_list_meta\">";
                            if ($show_meta_headings) {
                                echo "Series: ";
                            }
                            echo "{$item['series']}";
                            echo "</h6>";
                        }
                        if ($show_author && $item['author']) {
                            echo "<h6 class=\"ekklesia360_article_list_meta\">";
                            if ($show_meta_headings) {
                                echo "Author: ";
                            }
                            echo "{$item['author']}";
                            echo "</h6>";
                        }
                        if ($show_media_links) {
                            echo "<ul class=\"ekklesia360_article_list_media\">";
                            if ($item['videoplayer']) {
                                $item['videoplayer'] = preg_replace(
                                    '/<a(.+?)>.+?<\/a>/i',
                                    "<a$1><i class=\"fas fa-desktop\"></i></a>",
                                    $item['videoplayer']
                                );
                                echo "<li class=\"ekklesia360_article_list_media_videoplayer\">{$item['videoplayer']}</li>";
                            }
                            if ($item['audioplayer']) {
                                $item['audioplayer'] = preg_replace(
                                    '/<a(.+?)>.+?<\/a>/i',
                                    "<a$1><i class=\"fas fa-volume-up\"></i></a>",
                                    $item['audioplayer']
                                );
                                echo "<li class=\"ekklesia360_article_list_media_audioplayer\">{$item['audioplayer']}</li>";
                            }
                            if ($item['notes']) {
                                echo "<li class=\"ekklesia360_article_list_media_notes\"><a href=\"{$item['notes']}\" target=\"_blank\"><i class=\"fas fa-file-alt\"></i></a></li>";
                            }
                            echo "</ul>";
                        }
                        if ($show_preview && $item['preview']) {
                            $item['preview'] = substr($item['preview'], 0, 110)." ...";
                            echo "<p class=\"ekklesia360_article_list_preview\">{$item['preview']}</p>";
                        }

                        if ($detail_url && $detail_page_button_text) {
                            echo "<p class=\"ekklesia360_article_list_detail_button\"><a href=\"{$detail_url}?mc-slug={$item['slug']}\" class=\"elementor-button-link elementor-button elementor-size-sm\"><span class=\"elementor-button-text\">$detail_page_button_text</span></a></p>";
                        }

                        echo "</div>";
                        echo "</article>";
                    }
                    ?>
                </div>
                <?php
                if ($show_pagination && $content['after_show']['pagination']) {
                    $content['after_show']['pagination'] = str_replace('id="pagination"', 'class="ekklesia360_pagination"', $content['after_show']['pagination']);
                    $content['after_show']['pagination'] = str_replace('page=', 'mc-page=', $content['after_show']['pagination']);

                    echo $content['after_show']['pagination'];
                }
                ?>
            <?php else: ?>
                <p>There are no articles available.</p>
            <?php endif; ?>
        </div>
        <?php
    }
}
