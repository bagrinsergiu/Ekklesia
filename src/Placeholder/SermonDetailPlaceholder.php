<?php

namespace BrizyEkklesia\Placeholder\Ekklesia360;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class SermonDetailPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_sermon_detail';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'show_image'                => true,
            'show_video'                => true,
            'show_audio'                => true,
            'show_inline_video'         => true,
            'show_inline_audio'         => true,
            'show_media_links_video'    => true,
            'show_media_links_audio'    => true,
            'show_media_links_download' => true,
            'show_media_links_notes'    => true,
            'show_title'                => true,
            'show_date'                 => true,
            'show_category'             => true,
            'show_group'                => true,
            'show_series'               => true,
            'show_preacher'             => true,
            'show_passage'              => true,
            'show_meta_headings'        => true,
            'show_content'              => true,
        ];

        $settings = array_merge($options, $placeholder->getAttributes());
        $cms      = $this->monkCMS;

        extract($settings);

        //recent sermon
        $recent = $cms->get([
            'module'      => 'sermon',
            'display'     => 'list',
            'order'       => 'recent',
            'howmany'     => 1,
            'emailencode' => 'no',
            'show'        => "__videoplayer fullscreen='true'__",
            'show'        => "__audioplayer__",
        ]);

        //make slug...would be from widget-sermon-list.php
        if (isset($_GET['ekklesia360_sermon_slug'])) {
            $slug = $_GET['ekklesia360_sermon_slug'];
        } elseif (isset($settings['sermons_recent'])) {
            $slug = $settings['sermons_recent'];
        } else {
            $slug = $recent['show'][0]['slug'];
        }

        $content = $cms->get([
            'module'      => 'sermon',
            'display'     => 'detail',
            'find'        => $slug,
            'emailencode' => 'no',
            'show'        => "__videoplayer fullscreen='true'__",
            'show'        => "__audioplayer__",
        ]);

        ?>

        <div class="ekklesia360_sermon_detail_wrap">

            <?php //output
            if (count($content['show']) > 0) {
                $item = $content['show'];
                ?>

                <div class="ekklesia360_sermon_detail">
                    <?php
                    echo "<article>";
                    echo "<div class=\"info\">";
                    if ($show_title) {
                        echo "<h2 class=\"ekklesia360_sermon_detail_heading\">{$item['title']}</h2>";
                    }
                    if ($show_date && $item['date']) {
                        echo "<h6 class=\"ekklesia360_sermon_detail_meta\">";
                        if ($show_meta_headings) echo "Date: ";
                        echo "{$item['date']}";
                        echo "</h6>";
                    }
                    if ($show_category && $item['category']) {
                        echo "<h6 class=\"ekklesia360_sermon_detail_meta\">";
                        if ($show_meta_headings) echo "Category: ";
                        echo "{$item['category']}";
                        echo "</h6>";
                    }
                    if ($show_group && $item['group']) {
                        echo "<h6 class=\"ekklesia360_sermon_detail_meta\">";
                        if ($show_meta_headings) echo "Group: ";
                        echo "{$item['group']}";
                        echo "</h6>";
                    }
                    if ($show_series && $item['series']) {
                        echo "<h6 class=\"ekklesia360_sermon_detail_meta\">";
                        if ($show_meta_headings) echo "Series: ";
                        echo "{$item['series']}";
                        echo "</h6>";
                    }
                    if ($show_preacher && $item['preacher']) {
                        echo "<h6 class=\"ekklesia360_sermon_detail_meta\">";
                        if ($show_meta_headings) echo "Speaker: ";
                        echo "{$item['preacher']}";
                        echo "</h6>";
                    }
                    if ($show_passage && $item['passages']) {
                        echo "<h6 class=\"ekklesia360_sermon_detail_meta\">";
                        if ($show_meta_headings) echo "Passages: ";
                        echo "{$item['passages']}";
                        echo "</h6>";
                    }
                    if ($show_image && $item['imageurl'] && !$show_video) {
                        echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
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
                            echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                        }
                    }

                    if ($show_audio && $item['audiourl']) {
                        echo "<div class=\"ekklesia360_sermon_media_audio\">";
                        echo "<audio src=\"{$item['audiourl']}\" controls preload=\"none\"></audio>";
                        echo "</div>";
                    }
                    if ($show_media_links_video || $show_media_links_audio || $show_media_links_download || $show_media_links_notes) {
                        echo "<ul class=\"ekklesia360_sermon_detail_media\">";
                        if ($show_media_links_video && !empty($item['videoplayer'])) {
                            if (strpos($item['videoplayer'], 'class=')) {
                                $item['videoplayer'] = str_replace("Launch Player", "Watch", $item['videoplayer']);
                                $item['videoplayer'] = str_replace("Watch Video", "Watch", $item['videoplayer']);
                                $item['videoplayer'] = str_replace('mcms_videoplayer', 'mcms_videoplayer brz-button-link brz-button brz-size-sm ', $item['videoplayer']);
                            } else {
                                $item['videoplayer'] = str_replace(">Launch Player", " class=\"brz-button-link brz-button brz-size-sm\">Watch", $item['videoplayer']);
                                $item['videoplayer'] = str_replace(">Watch Video", " class=\"brz-button-link brz-button brz-size-sm\">Watch", $item['videoplayer']);
                            }

                            echo "<li class=\"ekklesia360_sermon_detail_media_videoplayer\">{$item['videoplayer']}</li>";
                        }

                        if ($show_media_links_audio && !empty($item['audioplayer'])) {
                            $item['audioplayer'] = str_replace("Launch Player", "Listen", $item['audioplayer']);
                            $item['audioplayer'] = str_replace('mcms_audioplayer', 'mcms_audioplayer brz-button-link brz-button brz-size-sm ', $item['audioplayer']);
                            echo "<li class=\"ekklesia360_sermon_detail_media_audioplayer\">{$item['audioplayer']}</li>";
                        }

                        if ($show_media_links_download && !empty($item['audio'])) {
                            echo "<li class=\"ekklesia360_sermon_detail_media_audiodownload\"><a href=\"{$item['audio']}\" class=\"brz-button-link brz-button brz-size-sm\">Download Audio</a></li>";
                        }

                        if ($show_media_links_notes && !empty($item['notes'])) {
                            echo "<li class=\"ekklesia360_sermon_detail_media_notes\"><a href=\"{$item['notes']}\" class=\"brz-button-link brz-button brz-size-sm\" target=\"_blank\">Notes</a></li>";
                        }

                        echo "</ul>";
                    }

                    if ($show_content && !empty($item['text'])) {
                        echo "<div class=\"ekklesia360_sermon_detail_content\">{$item['text']}</div>";
                    }

                    echo "<p class=\"ekklesia360_sermon_detail_previous\"><a href=\"javascript:history.back();\"><i class=\"fas fa-angle-left\"></i> Previous Page</a></p>";

                    echo "</div>";
                    echo "</article>";
                    ?>
                </div>
                <?php

            } else {
                ?>
                <p>There is no sermon available.</p>
                <?php
            }
            ?>
        </div>
        <?php
    }
}