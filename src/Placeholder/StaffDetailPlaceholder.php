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
            'show_groups'        => false,
            'show_phone_work'    => false,
            'show_phone_cell'    => false,
            'show_email'         => true,
            'show_facebook'      => true,
            'show_twitter'       => true,
            'show_instagram'     => false,
            'show_website'       => false,
            'show_rss'           => false,
            'show_meta_headings' => true,
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

        <div class="brz-staffDetail__container">
            <?php if (!empty($content['show'])) {
                $item = $content['show'];
                ?>
                <div class="brz-staffDetail__item">
                    <?php
                    if ($show_image && $item['photourl']) {
                        echo "<div class=\"brz-ministryBrands__item--media\">
                        <img src=\"{$item['photourl']}\" alt=\"\" />
                        </div>";
                    }

                    if ($show_title) {
                        echo "<h3 class=\"brz-staffDetail__item--meta--title\">{$item['fullname']}</h3>";
                    }

                    if ($show_position && $item['position']) {
                        echo "<h6 class=\"brz-staffDetail__item--meta\">{$item['position']}</h6>";
                    }

                    if ($show_groups && $item['groups']) {
                        echo "<h6 class=\"brz-staffDetail__item--meta\">";
                        if ($show_meta_headings) {
                            echo "Groups: ";
                        }
                        echo "{$item['groups']}";
                        echo "</h6>";
                    }

                    if ($show_phone_work && $item['workphone']) {
                        echo "<h6 class=\"brz-staffDetail__item--meta\">";
                        if ($show_meta_headings) {
                            echo "Phone: ";
                        }
                        echo "{$item['workphone']}";
                        echo "</h6>";
                    }

                    if ($show_phone_cell && $item['cellphone']) {
                        echo "<h6 class=\"brz-staffDetail__item--meta\">";
                        if ($show_meta_headings) {
                            echo "Cell: ";
                        }
                        echo "{$item['cellphone']}";
                        echo "</h6>";
                    }

                    echo "<ul class=\"brz-staffDetail__item--social\">";

                    if ($show_email && ($item['emailaddress'] || $item['altemailaddress'])) {
                        if ($item['altemailaddress']) {
                            $item['emailaddress'] = $item['altemailaddress'];
                        }
                        echo "<li><a href=\"mailto:{$item['emailaddress']}\" title=\"Email\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\"><path d=\"M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z\"/></svg></a></li>";
                    }

                    if ($show_facebook && $item['facebookurl']) {
                        echo "<li><a href=\"{$item['facebookurl']}\" title=\"Facebook\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 320 512\"><path d=\"M279.1 288l14.2-92.7h-88.9v-60.1c0-25.4 12.4-50.1 52.2-50.1h40.4V6.3S260.4 0 225.4 0c-73.2 0-121.1 44.4-121.1 124.7v70.6H22.9V288h81.4v224h100.2V288z\"/></svg></a></li>";
                    }

                    if ($show_twitter && $item['twitterurl']) {
                        echo "<li><a href=\"{$item['twitterurl']}\" title=\"Twitter\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\"><path d=\"M459.4 151.7c.3 4.5 .3 9.1 .3 13.6 0 138.7-105.6 298.6-298.6 298.6-59.5 0-114.7-17.2-161.1-47.1 8.4 1 16.6 1.3 25.3 1.3 49.1 0 94.2-16.6 130.3-44.8-46.1-1-84.8-31.2-98.1-72.8 6.5 1 13 1.6 19.8 1.6 9.4 0 18.8-1.3 27.6-3.6-48.1-9.7-84.1-52-84.1-103v-1.3c14 7.8 30.2 12.7 47.4 13.3-28.3-18.8-46.8-51-46.8-87.4 0-19.5 5.2-37.4 14.3-53 51.7 63.7 129.3 105.3 216.4 109.8-1.6-7.8-2.6-15.9-2.6-24 0-57.8 46.8-104.9 104.9-104.9 30.2 0 57.5 12.7 76.7 33.1 23.7-4.5 46.5-13.3 66.6-25.3-7.8 24.4-24.4 44.8-46.1 57.8 21.1-2.3 41.6-8.1 60.4-16.2-14.3 20.8-32.2 39.3-52.6 54.3z\"/></svg></a></li>";
                    }

                    if ($show_instagram && $item['instagramurl']) {
                        echo "<li><a href=\"{$item['instagramurl']}\" title=\"Instagram\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path d=\"M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z\"/></svg></a></li>";
                    }

                    if ($show_website && $item['websiteurl']) {
                        echo "<li><a href=\"{$item['websiteurl']}\" title=\"Website\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\"><path d=\"M352 256c0 22.2-1.2 43.6-3.3 64H163.3c-2.2-20.4-3.3-41.8-3.3-64s1.2-43.6 3.3-64H348.7c2.2 20.4 3.3 41.8 3.3 64zm28.8-64H503.9c5.3 20.5 8.1 41.9 8.1 64s-2.8 43.5-8.1 64H380.8c2.1-20.6 3.2-42 3.2-64s-1.1-43.4-3.2-64zm112.6-32H376.7c-10-63.9-29.8-117.4-55.3-151.6c78.3 20.7 142 77.5 171.9 151.6zm-149.1 0H167.7c6.1-36.4 15.5-68.6 27-94.7c10.5-23.6 22.2-40.7 33.5-51.5C239.4 3.2 248.7 0 256 0s16.6 3.2 27.8 13.8c11.3 10.8 23 27.9 33.5 51.5c11.6 26 20.9 58.2 27 94.7zm-209 0H18.6C48.6 85.9 112.2 29.1 190.6 8.4C165.1 42.6 145.3 96.1 135.3 160zM8.1 192H131.2c-2.1 20.6-3.2 42-3.2 64s1.1 43.4 3.2 64H8.1C2.8 299.5 0 278.1 0 256s2.8-43.5 8.1-64zM194.7 446.6c-11.6-26-20.9-58.2-27-94.6H344.3c-6.1 36.4-15.5 68.6-27 94.6c-10.5 23.6-22.2 40.7-33.5 51.5C272.6 508.8 263.3 512 256 512s-16.6-3.2-27.8-13.8c-11.3-10.8-23-27.9-33.5-51.5zM135.3 352c10 63.9 29.8 117.4 55.3 151.6C112.2 482.9 48.6 426.1 18.6 352H135.3zm358.1 0c-30 74.1-93.6 130.9-171.9 151.6c25.5-34.2 45.2-87.7 55.3-151.6H493.4z\"/></svg></a></li>";
                    }

                    if ($show_rss && $item['rssfeedurl']) {
                        echo "<li><a href=\"{$item['rssfeedurl']}\" title=\"RSS\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path d=\"M128.1 416c0 35.4-28.7 64-64 64S0 451.3 0 416s28.7-64 64-64 64 28.7 64 64zm175.7 47.3c-8.4-154.6-132.2-278.6-287-287C7.7 175.8 0 183.1 0 192.3v48.1c0 8.4 6.5 15.5 14.9 16 111.8 7.3 201.5 96.7 208.8 208.8 .5 8.4 7.6 14.9 16 14.9h48.1c9.1 0 16.5-7.7 16-16.8zm144.2 .3C439.6 229.7 251.5 40.4 16.5 32 7.5 31.7 0 39 0 48v48.1c0 8.6 6.8 15.6 15.5 16 191.2 7.8 344.6 161.3 352.5 352.5 .4 8.6 7.4 15.5 16 15.5h48.1c9 0 16.3-7.5 16-16.5z\"/></svg></a></li>";
                    }

                    echo "</ul>";

                    if ($show_about && $item['about']) {
                        echo "<div class=\"brz-staffDetail__item--about\">{$item['about']}</div>";
                    }

                    echo '<div class="brz-ministryBrands__item--meta--links brz-ministryBrands__item--meta--links--previous">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" class="brz-icon-svg align-[initial]"><path d="M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z"></path></svg>
                    Previous Page</div>';
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
