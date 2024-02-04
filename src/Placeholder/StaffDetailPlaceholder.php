<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class StaffDetailPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_staff_detail';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $settings = array_merge([
                'staff_recent'       => '',
                'show_image'         => true,
                'show_title'         => true,
                'show_position'      => true,
                'show_groups'        => true,
                'show_phone_work'    => false,
                'show_phone_cell'    => false,
                'show_email'         => false,
                'show_facebook'      => false,
                'show_twitter'       => false,
                'show_instagram'     => false,
                'show_website'       => false,
                'show_rss'           => false,
                'show_meta_headings' => false,
                'show_about'         => true,
        ], $placeholder->getAttributes());

        extract($settings);

        $cms  = $this->monkCMS;
        $slug = isset($_GET['mc-slug']) ? $_GET['mc-slug'] : $staff_recent;

        if (!$slug) {
            $recent = $cms->get([
                'module'      => 'member',
                'display'     => 'list',
                'order'       => 'position',
                'howmany'     => 1,
                'emailencode' => 'no',
                'restrict'    => 'no',
            ]);

            if (empty($recent['show'][0]['id'])) {
                return;
            }

            $slug = $recent['show'][0]['id'];
        }

        $content = $cms->get([
            'module'      => 'member',
            'display'     => 'profile',
            'emailencode' => 'no',
            'restrict'    => 'no',
            'find'        => $slug,
        ]);
        ?>

        <div class="ekklesia360_staff_detail_wrap">
            <?php if (!empty($content['show'])) {
                $item = $content['show'];
                ?>
                <div class="ekklesia360_staff_detail">
                    <?php
                    echo "<article>";
                    if ($show_image && $item['photourl']) {
                        echo "<div class=\"image\"><img src=\"{$item['photourl']}\" alt=\"\" /></div>";
                    }
                    echo "<div class=\"info\">";

                    if ($show_title) {
                        echo "<h3 class=\"ekklesia360_staff_detail_heading\">{$item['fullname']}</h3>";
                    }

                    if ($show_position && $item['position']) {
                        echo "<h6 class=\"ekklesia360_staff_detail_position\">{$item['position']}</h6>";
                    }

                    if ($show_groups && $item['groups']) {
                        echo "<p class=\"ekklesia360_staff_detail_meta\">";
                        if ($show_meta_headings) {
                            echo "Groups: ";
                        }
                        echo "{$item['groups']}";
                        echo "</p>";
                    }

                    if ($show_phone_work && $item['workphone']) {
                        echo "<p class=\"ekklesia360_staff_detail_meta\">";
                        if ($show_meta_headings) {
                            echo "Phone: ";
                        }
                        echo "{$item['workphone']}";
                        echo "</p>";
                    }

                    if ($show_phone_cell && $item['cellphone']) {
                        echo "<p class=\"ekklesia360_staff_detail_meta\">";
                        if ($show_meta_headings) {
                            echo "Cell: ";
                        }
                        echo "{$item['cellphone']}";
                        echo "</p>";
                    }

                    echo "<ul class=\"ekklesia360_staff_detail_social\">";
                    if ($show_email && ($item['emailaddress'] || $item['altemailaddress'])) {
                        if ($item['altemailaddress']) {
                            $item['emailaddress'] = $item['altemailaddress'];
                        }
                        echo "<li><a href=\"mailto:{$item['emailaddress']}\" title=\"Email\"><span class=\"fas fa-envelope\"></span></a></li>";
                    }
                    if ($show_facebook && $item['facebookurl']) {
                        echo "<li><a href=\"{$item['facebookurl']}\" title=\"Facebook\" target=\"_blank\"><span class=\"fab fa-facebook-f\"></span></a></li>";
                    }
                    if ($show_twitter && $item['twitterurl']) {
                        echo "<li><a href=\"{$item['twitterurl']}\" title=\"Twitter\" target=\"_blank\"><span class=\"fab fa-twitter\"></span></a></li>";
                    }
                    if ($show_instagram && $item['instagramurl']) {
                        echo "<li><a href=\"{$item['instagramurl']}\" title=\"Instagram\" target=\"_blank\"><span class=\"fab fa-instagram\"></span></a></li>";
                    }
                    if ($show_website && $item['websiteurl']) {
                        echo "<li><a href=\"{$item['websiteurl']}\" title=\"Website\" target=\"_blank\"><span class=\"fas fa-globe\"></span></a></li>";
                    }
                    if ($show_rss && $item['rssfeedurl']) {
                        echo "<li><a href=\"{$item['rssfeedurl']}\" title=\"RSS\" target=\"_blank\"><span class=\"fas fa-rss\"></span></a></li>";
                    }
                    echo "</ul>";

                    if ($show_about && $item['about']) {
                        echo "<div class=\"ekklesia360_staff_detail_about\">{$item['about']}</div>";
                    }

                    echo "</div>";
                    echo "</article>";
                    ?>
                </div>
                <?php
            } else {
                ?>

                <p>There are no staff available.</p>

                <?php
            }
            ?>
        </div>
        <?php
    }
}
