<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class StaffListPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_staff_list';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $settings = array_merge([
            'group'                   => '',
            'show_images'             => true,
            'show_title'              => true,
            'show_position'           => true,
            'show_groups'             => false,
            'show_phone_work'         => false,
            'show_phone_cell'         => false,
            'show_email'              => false,
            'show_facebook'           => false,
            'show_twitter'            => false,
            'show_website'            => false,
            'show_instagram'          => false,
            'show_meta_headings'      => false,
            'detail_page_button_text' => '',
            'detail_page'             => '',
        ], $placeholder->getAttributes());

        extract($settings);

        $detail_url = $detail_page ? $this->replacer->replacePlaceholders(urldecode($detail_page), $context) : '';
        $cms        = $this->monkCMS;
        $content    = $cms->get([
            'module'      => 'member',
            'display'     => 'list',
            'order'       => 'position',
            'emailencode' => 'no',
            'restrict'    => 'no',
            'find_group'  => $group,
        ]);
        ?>
        <div class="brz-staffList__wrap">
            <?php if (!empty($content['show'])) { ?>
                <div class="brz-staffList__container">
                    <?php
                    foreach ($content['show'] as $item) {
                        echo "<article>";
                        if ($show_images && $item['photourl']) {
                            echo "<div class=\"brz-staffList__image\">";
                            echo "<img src=\"{$item['photourl']}\" alt=\"\" />";

                            echo "<div class='brz-staffList__rollover'>";
                            echo "<div class='brz-staffList__rollover_inner'>";
                            if ($detail_url) {
                                echo "<a class='brz-staffList__detail-url' href=\"{$detail_url}?mc-slug={$item['id']}\">";
                                echo "<span class='brz-staffList__spacer'></span>";
                                echo "<p>";
                                echo "<span class='brz-staffList__detail_page--button-text'>$detail_page_button_text</span>";
                                echo "</p>";
                                echo "</a>";
                            }
                            echo "</div>";

                            echo "<ul class=\"brz-staffList__social\">";
                            if ($show_email && ($item['emailaddress'] || $item['altemailaddress'])) {
                                if ($item['altemailaddress']) {
                                    $item['emailaddress'] = $item['altemailaddress'];
                                }
                                echo "<li><a class='brz-staffList__link' href=\"mailto:{$item['emailaddress']}\" title=\"Email\"><span class=\"brz-icon fas fa-envelope\"></span></a></li>";
                            }
                            if ($show_facebook && $item['facebookurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['facebookurl']}\" title=\"Facebook\" target=\"_blank\"><span class=\"brz-icon fab fa-facebook-f\"></span></a></li>";
                            }
                            if ($show_twitter && $item['twitterurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['twitterurl']}\" title=\"Twitter\" target=\"_blank\"><span class=\"brz-icon fab fa-twitter\"></span></a></li>";
                            }
                            if ($show_instagram && $item['instagramurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['instagramurl']}\" title=\"Instagram\" target=\"_blank\"><span class=\"brz-icon fab fa-instagram\"></span></a></li>";
                            }
                            if ($show_website && $item['websiteurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['websiteurl']}\" title=\"Website\" target=\"_blank\"><span class=\"brz-icon fas fa-globe\"></span></a></li>";
                            }

                            echo "</ul>";
                            echo "</div>";
                            echo "</div>";
                        }
                        echo "<div class=\"brz-staffList__info\">";

                        if ($show_title) {
                            echo "<h4 class=\"brz-staffList__heading\">";
                            if ($detail_url) {
                                echo "<a class='brz-staffList__link_detail' href=\"{$detail_url}?mc-slug={$item['id']}\">";
                            }
                            echo "{$item['fullname']}";
                            if ($detail_url) {
                                echo "</a>";
                            }
                            echo "</h4>";
                        }

                        if ($show_position && $item['position']) {
                            echo "<h6 class=\"brz-staffList__position\">{$item['position']}</h6>";
                        }

                        if ($show_groups && $item['groups']) {
                            echo "<p class=\"brz-staffList__meta\">";
                            if ($show_meta_headings) {
                                echo "Groups: ";
                            }
                            echo "{$item['groups']}";
                            echo "</p>";
                        }

                        if ($show_phone_work && $item['workphone']) {
                            echo "<p class=\"brz-staffList__meta\">";
                            if ($show_meta_headings) {
                                echo "Phone: ";
                            }
                            echo "{$item['workphone']}";
                            echo "</p>";
                        }

                        if ($show_phone_cell && $item['cellphone']) {
                            echo "<p class=\"brz-staffList__meta\">";
                            if ($show_meta_headings) {
                                echo "Cell: ";
                            }
                            echo "{$item['cellphone']}";
                            echo "</p>";
                        }

                        if (!$show_images) {
                            echo "<ul class=\"brz-staffList__social brz-staffList_no-image\">";
                            if ($show_email && ($item['emailaddress'] || $item['altemailaddress'])) {
                                if ($item['altemailaddress']) {
                                    $item['emailaddress'] = $item['altemailaddress'];
                                }
                                echo "<li><a class='brz-staffList__link' href=\"mailto:{$item['emailaddress']}\" title=\"Email\"><span class=\"brz-icon fas fa-envelope\"></span></a></li>";
                            }
                            if ($show_facebook && $item['facebookurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['facebookurl']}\" title=\"Facebook\" target=\"_blank\"><span class=\"brz-icon fab fa-facebook-f\"></span></a></li>";
                            }
                            if ($show_twitter && $item['twitterurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['twitterurl']}\" title=\"Twitter\" target=\"_blank\"><span class=\"brz-icon fab fa-twitter\"></span></a></li>";
                            }
                            if ($show_instagram && $item['instagramurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['instagramurl']}\" title=\"Instagram\" target=\"_blank\"><span class=\"brz-icon fab fa-instagram\"></span></a></li>";
                            }
                            if ($show_website && $item['websiteurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['websiteurl']}\" title=\"Website\" target=\"_blank\"><span class=\"brz-icon fas fa-globe\"></span></a></li>";
                            }
                            echo "</ul>";
                        }

                        echo "</div>";
                        echo "</article>";
                    }
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
