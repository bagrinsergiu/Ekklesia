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
            'show_meta_icons'         => false,
            'howmany'                 => 9,
            'detail_page_button_text' => '',
            'detail_page'             => '',
            'show_full_email'         => false,
            'show_pagination'         => true,
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

        $_content  = isset($content["show"]) ? $content["show"] : [];
        $page      = isset($_GET['mc-page']) ? $_GET['mc-page'] : 1;

        $pagination = new CustomPagination($_content , (isset($page) ? $page : 1), $howmany);
        $pagination->setShowFirstAndLast(true);
        $resultsPagination = $pagination->getResults();
        ?>
        <div class="brz-staffList__wrap">
            <?php if (count($resultsPagination) > 0) { ?>
                <div class="brz-staffList__container">
                    <?php
                    foreach ($resultsPagination as $key => $item) {
                        echo "<article>";
                        if ($show_images && $item['photourl']) {
                            echo "<div class=\"brz-ministryBrands__item--media\">";
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
                            if (!$item['customhideemail'] && !$show_full_email && $show_email && ($item['emailaddress'] || $item['altemailaddress'])) {
                                $item['emailaddress'] = $item['customdisplayemail'] ?: ($item['altemailaddress'] ?: $item['emailaddress']);
                                $encoded_email = base64_encode($item['emailaddress']);
                                echo "<li><a class='brz-staffList__link' data-brz-email=\"{$encoded_email}\" title=\"Email\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\" class=\"brz-icon-svg align-[initial]\"><path d=\"M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z\"></path></svg></a></li>";
                            }
                            if ($show_facebook && $item['facebookurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['facebookurl']}\" title=\"Facebook\" target=\"_blank\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 320 512\" class=\"brz-icon-svg align-[initial]\"><path d=\"m279.14 288 14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z\"></path></svg></a></li>";
                            }
                            if ($show_twitter && $item['twitterurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['twitterurl']}\" title=\"Twitter\" target=\"_blank\"><svg xmlns='http://www.w3.org/2000/svg' fill='currentColor' class='brz-icon-svg align-[initial]' viewBox='0 0 16 16'><path d='M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z'/></svg></a></li>";
                            }
                            if ($show_instagram && $item['instagramurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['instagramurl']}\" title=\"Instagram\" target=\"_blank\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\" class=\"brz-icon-svg align-[initial]\"><path d=\"M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z\"></path></svg></a></li>";
                            }
                            if ($show_website && $item['websiteurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['websiteurl']}\" title=\"Website\" target=\"_blank\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 496 512\" class=\"brz-icon-svg align-[initial]\"><path d=\"M336.5 160C322 70.7 287.8 8 248 8s-74 62.7-88.5 152h177zM152 256c0 22.2 1.2 43.5 3.3 64h185.3c2.1-20.5 3.3-41.8 3.3-64s-1.2-43.5-3.3-64H155.3c-2.1 20.5-3.3 41.8-3.3 64zm324.7-96c-28.6-67.9-86.5-120.4-158-141.6 24.4 33.8 41.2 84.7 50 141.6h108zM177.2 18.4C105.8 39.6 47.8 92.1 19.3 160h108c8.7-56.9 25.5-107.8 49.9-141.6zM487.4 192H372.7c2.1 21 3.3 42.5 3.3 64s-1.2 43-3.3 64h114.6c5.5-20.5 8.6-41.8 8.6-64s-3.1-43.5-8.5-64zM120 256c0-21.5 1.2-43 3.3-64H8.6C3.2 212.5 0 233.8 0 256s3.2 43.5 8.6 64h114.6c-2-21-3.2-42.5-3.2-64zm39.5 96c14.5 89.3 48.7 152 88.5 152s74-62.7 88.5-152h-177zm159.3 141.6c71.4-21.2 129.4-73.7 158-141.6h-108c-8.8 56.9-25.6 107.8-50 141.6zM19.3 352c28.6 67.9 86.5 120.4 158 141.6-24.4-33.8-41.2-84.7-50-141.6h-108z\"></path></svg></a></li>";
                            }
                            if ($show_rss && $item['rssfeedurl']) {
                                echo "<li><a href=\"{$item['rssfeedurl']}\" title=\"RSS\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path d=\"M128.1 416c0 35.4-28.7 64-64 64S0 451.3 0 416s28.7-64 64-64 64 28.7 64 64zm175.7 47.3c-8.4-154.6-132.2-278.6-287-287C7.7 175.8 0 183.1 0 192.3v48.1c0 8.4 6.5 15.5 14.9 16 111.8 7.3 201.5 96.7 208.8 208.8 .5 8.4 7.6 14.9 16 14.9h48.1c9.1 0 16.5-7.7 16-16.8zm144.2 .3C439.6 229.7 251.5 40.4 16.5 32 7.5 31.7 0 39 0 48v48.1c0 8.6 6.8 15.6 15.5 16 191.2 7.8 344.6 161.3 352.5 352.5 .4 8.6 7.4 15.5 16 15.5h48.1c9 0 16.3-7.5 16-16.5z\"/></svg></a></li>";
                            }
                            echo "</ul>";
                            echo "</div>";
                            echo "</div>";
                        }
                        echo "<div class=\"brz-staffList__info brz-staffList__item\">";

                        if ($show_title) {
                            echo "<h4 class=\"brz-staffList__heading brz-staffList__item\">";
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
                            echo "<h6 class=\"brz-staffList__position brz-staffList__item--meta\">{$item['position']}</h6>";
                        }

                        if ($show_groups && $item['groups']) {
                            echo "<p class=\"brz-ministryBrands__item--meta-group brz-staffList__item--meta\">";

                            if ($show_meta_headings || $show_meta_icons) {
                                echo "<span class=\"brz-ministryBrands__item--wrapper\">";

                                if ($show_meta_icons) {
                                    echo "<span class=\"brz-ministryBrands__meta--icons\">
                                            <svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 640 512\">
                                                <path fill=\"currentColor\" d=\"M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z\"/>
                                            </svg>
                                          </span>";
                                }

                                if ($show_meta_headings) {
                                    echo "Groups: {$item['groups']}";
                                } else {
                                    echo "{$item['groups']}";
                                }
                            } else {
                                echo "{$item['groups']}";
                            }

                            echo "</span></p>";
                        }

                        if (!$item['customhideemail'] && $show_full_email && ($item['emailaddress'] || $item['altemailaddress'])) {
                            $item['emailaddress'] = $item['customdisplayemail'] ?: ($item['altemailaddress'] ?: $item['emailaddress']);
                            echo "<p class=\"brz-staffList__item--meta brz-ministryBrands__item--meta-full-email\">";
                            if ($show_meta_headings) {
                                if ($show_meta_icons) {
                                    echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\"><path d=\"M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z\"/></svg></span>";
                                } else {
                                    echo "<span>Email: </span>";
                                }
                            }
                            $encoded_email = base64_encode($item['emailaddress']);
                            echo "<a class='brz-staffList__link brz-staffList__link--full-email' data-brz-email=\"{$encoded_email}\" title=\"Email\">{$item['emailaddress']}</a>";
                            echo "</p>";
                        }


                        if ($show_phone_work && $item['workphone']) {
                            echo "<p class=\"brz-ministryBrands__item--meta-workphone brz-staffList__item--meta\">";

                            if ($show_meta_headings || $show_meta_icons) {
                                echo "<span class=\"brz-ministryBrands__item--wrapper\">";

                                if ($show_meta_icons) {
                                    echo "<span class=\"brz-ministryBrands__meta--icons\">
                                            <svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 384 512\">
                                                <path d=\"M16 64C16 28.7 44.7 0 80 0H304c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H80c-35.3 0-64-28.7-64-64V64zM224 448a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM304 64H80V384H304V64z\"/>
                                            </svg>
                                          </span>";
                                }

                                if ($show_meta_headings) {
                                    echo "Phone: {$item['workphone']}";
                                } else {
                                    echo "{$item['workphone']}";
                                }
                            } else {
                                echo "{$item['workphone']}";
                            }

                            echo "</span></p>";
                        }


                        if ($show_phone_cell && $item['cellphone']) {
                            echo "<p class=\"brz-ministryBrands__item--meta-cellphone brz-staffList__item--meta\">";

                            if ($show_meta_headings || $show_meta_icons) {
                                echo "<span class=\"brz-ministryBrands__item--wrapper\">";

                                if ($show_meta_icons) {
                                    echo "<span class=\"brz-ministryBrands__meta--icons\">
                                            <svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 384 512\">
                                                <path d=\"M16 64C16 28.7 44.7 0 80 0H304c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H80c-35.3 0-64-28.7-64-64V64zM224 448a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM304 64H80V384H304V64z\"/>
                                            </svg>
                                          </span>";
                                }

                                 if ($show_meta_headings) {
                                     echo "Cell: {$item['cellphone']}";
                                 } else {
                                     echo "{$item['cellphone']}";
                                 }
                            } else {
                                echo "{$item['cellphone']}";
                            }

                            echo "</span></p>";
                        }

                        if (!$show_images) {
                            echo "<ul class=\"brz-staffList__social brz-staffList_no-image\">";
                            if (!$item['customhideemail'] && !$show_full_email && $show_email && ($item['emailaddress'] || $item['altemailaddress'])) {
                                $item['emailaddress'] = $item['customdisplayemail'] ?: ($item['altemailaddress'] ?: $item['emailaddress']);
                                $encoded_email = base64_encode($item['emailaddress']);
                                echo "<li><a class='brz-staffList__link' data-brz-email=\"{$encoded_email}\" title=\"Email\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\"><path d=\"M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z\"/></svg></a></li>";
                            }
                            if ($show_facebook && $item['facebookurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['facebookurl']}\" title=\"Facebook\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 320 512\"><path d=\"M279.1 288l14.2-92.7h-88.9v-60.1c0-25.4 12.4-50.1 52.2-50.1h40.4V6.3S260.4 0 225.4 0c-73.2 0-121.1 44.4-121.1 124.7v70.6H22.9V288h81.4v224h100.2V288z\"/></svg></a></li>";
                            }
                            if ($show_twitter && $item['twitterurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['twitterurl']}\" title=\"Twitter\" target=\"_blank\"><svg xmlns='http://www.w3.org/2000/svg' fill='currentColor' class='brz-icon-svg align-[initial]' viewBox='0 0 16 16'><path d='M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z'/></svg></a></li>";
                            }
                            if ($show_instagram && $item['instagramurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['instagramurl']}\" title=\"Instagram\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path d=\"M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z\"/></svg></a></li>";
                            }
                            if ($show_website && $item['websiteurl']) {
                                echo "<li><a class='brz-staffList__link' href=\"{$item['websiteurl']}\" title=\"Website\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\"><path d=\"M352 256c0 22.2-1.2 43.6-3.3 64H163.3c-2.2-20.4-3.3-41.8-3.3-64s1.2-43.6 3.3-64H348.7c2.2 20.4 3.3 41.8 3.3 64zm28.8-64H503.9c5.3 20.5 8.1 41.9 8.1 64s-2.8 43.5-8.1 64H380.8c2.1-20.6 3.2-42 3.2-64s-1.1-43.4-3.2-64zm112.6-32H376.7c-10-63.9-29.8-117.4-55.3-151.6c78.3 20.7 142 77.5 171.9 151.6zm-149.1 0H167.7c6.1-36.4 15.5-68.6 27-94.7c10.5-23.6 22.2-40.7 33.5-51.5C239.4 3.2 248.7 0 256 0s16.6 3.2 27.8 13.8c11.3 10.8 23 27.9 33.5 51.5c11.6 26 20.9 58.2 27 94.7zm-209 0H18.6C48.6 85.9 112.2 29.1 190.6 8.4C165.1 42.6 145.3 96.1 135.3 160zM8.1 192H131.2c-2.1 20.6-3.2 42-3.2 64s1.1 43.4 3.2 64H8.1C2.8 299.5 0 278.1 0 256s2.8-43.5 8.1-64zM194.7 446.6c-11.6-26-20.9-58.2-27-94.6H344.3c-6.1 36.4-15.5 68.6-27 94.6c-10.5 23.6-22.2 40.7-33.5 51.5C272.6 508.8 263.3 512 256 512s-16.6-3.2-27.8-13.8c-11.3-10.8-23-27.9-33.5-51.5zM135.3 352c10 63.9 29.8 117.4 55.3 151.6C112.2 482.9 48.6 426.1 18.6 352H135.3zm358.1 0c-30 74.1-93.6 130.9-171.9 151.6c25.5-34.2 45.2-87.7 55.3-151.6H493.4z\"/></svg></a></li>";
                            }
                            if ($show_rss && $item['rssfeedurl']) {
                                echo "<li><a href=\"{$item['rssfeedurl']}\" title=\"RSS\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path d=\"M128.1 416c0 35.4-28.7 64-64 64S0 451.3 0 416s28.7-64 64-64 64 28.7 64 64zm175.7 47.3c-8.4-154.6-132.2-278.6-287-287C7.7 175.8 0 183.1 0 192.3v48.1c0 8.4 6.5 15.5 14.9 16 111.8 7.3 201.5 96.7 208.8 208.8 .5 8.4 7.6 14.9 16 14.9h48.1c9.1 0 16.5-7.7 16-16.8zm144.2 .3C439.6 229.7 251.5 40.4 16.5 32 7.5 31.7 0 39 0 48v48.1c0 8.6 6.8 15.6 15.5 16 191.2 7.8 344.6 161.3 352.5 352.5 .4 8.6 7.4 15.5 16 15.5h48.1c9 0 16.3-7.5 16-16.5z\"/></svg></a></li>";
                            }
                            echo "</ul>";
                        }

                        echo "</div>";
                        echo "</article>";
                    }
                    ?>
                </div>
                <?php
                if ($show_pagination) {
                    echo '<p id="brz-staffList__pagination" class="brz-staffList__pagination">' . $pagination->getLinks($_GET, 'mc-page') . '</p>';
                }
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