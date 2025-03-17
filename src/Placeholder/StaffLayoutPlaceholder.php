<?php
namespace BrizyEkklesia\Placeholder;
use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class StaffLayoutPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_staff_layout';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'show_group_filter'       => true,
            'show_search'             => true,
            'show_images'             => true,
            'show_title'              => true,
            'show_position'           => true,
            'show_groups'             => true,
            'show_phone_work'         => false,
            'show_phone_cell'         => false,
            'show_email'              => false,
            'show_facebook'           => false,
            'show_twitter'            => false,
            'show_website'            => false,
            'show_instagram'          => false,
            'show_meta_headings'      => false,
            'show_full_email'         => false,
            'detail_page_button_text' => 'Read More',
            'detail_page'             => '',
            'show_meta_icons'         => false,
            'search_placeholder'      => 'Search',
            'group_filter_heading'    => 'Group',
            'howmany'                 => 9,
            'show_pagination'         => true,
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $baseURL     = strtok($_SERVER['REQUEST_URI'], '?') !== false ? strtok($_SERVER['REQUEST_URI'], '?') : $_SERVER['REQUEST_URI'];
        $detail_url  = $detail_page ? $this->replacer->replacePlaceholders(urldecode($detail_page), $context) : '';
        $cms         = $this->monkCMS;
        $filterCount = count(array_filter([$show_group_filter]));
        $groups      = $show_group_filter ? $cms->get(['module' => 'group', 'display' => 'list',]) : [];
        $page      = isset($_GET['mc-page']) ? $_GET['mc-page'] : 1;

        if (isset($_GET['mc-search_term'])) {
            $content  = [];
            $keywords = array_filter(explode(' ', $_GET['mc-search_term']));
            $members  = $cms->get([
                'module'      => 'member',
                'display'     => 'list',
                'order'       => 'position',
                'emailencode' => 'no',
                'restrict'    => 'no',
            ]);

            foreach ($members['show'] as $member) {

                $search_string = implode(' ', array_intersect_key($member, array_fill_keys(['fullname', 'position', 'groups', 'emailaddress'], '')));

                foreach ($keywords as $keyword) {
                    if (stristr($search_string, $keyword)) {
                        $content['show'][] = $member;
                    }
                }
            }
        } else {
            $content = $cms->get([
                'module'      => 'member',
                'display'     => 'list',
                'order'       => 'position',
                'emailencode' => 'no',
                'restrict'    => 'no',
                'find_group'  => isset($_GET['mc-group']) ? $_GET['mc-group'] : '',
            ]);
        }

        $_content  = isset($content["show"]) ? $content["show"] : [];
        $pagination = new CustomPagination($_content , (isset($page) ? $page : 1), $howmany);
        $pagination->setShowFirstAndLast(true);
        $resultsPagination = $pagination->getResults();
?>

        <div id="brz-staffLayout__filters" class="brz-staffLayout__filters">
            
            <?php if ($show_group_filter && !empty($groups['show'])) : ?>
                <form id="brz-staffLayout__filters--form" name="brz-staffLayout__filters--form" class="brz-staffLayout__filters--form" action="<?= $baseURL ?>" data-count="<?= $filterCount ?>">
                    <div class="brz-staffLayout__filters--form-selectWrapper">
                        <select name="mc-group">
                        <option value=""><?= $group_filter_heading ?></option>
                            <option value="">All</option>
                            <?php
                            foreach ($groups['show'] as $group) {
                                echo "<option value=\"{$group['slug']}\"";
                                if (isset($_GET['mc-group']) && $_GET['mc-group'] == $group['slug']) {
                                    echo " selected";
                                }
                                echo ">{$group['title']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </form>
                <?php endif; ?>

            <?php if ($show_search) : ?>
                <form method="get" id="brz-staffLayout__filters--form-search" name="search" class="brz-staffLayout__filters--form-search" action="<?= $baseURL ?>" data-count="<?= $filterCount ?>">
                    <fieldset>
                        <input type="text" id="brz-staffLayout__filters--form-search_term" name="mc-search_term" value="" placeholder="<?= $search_placeholder ?>" />
                        <button type="submit" id="brz-staffLayout__filters--form-search_submit"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="brz-icon-svg align-[initial]" data-type="fa" data-name="search">
                                <path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path>
                            </svg></button>
                    </fieldset>
                </form>
            <?php endif; ?>
        </div>

        <?php
        if (isset($_GET['mc-search_term'])) {
            echo "<h4 class=\"brz-staffLayout-results-heading\"><a href=\"{$baseURL}\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 352 512\" class=\"brz-icon-svg align-[initial]\" data-type=\"fa\" data-name=\"times\"><path d=\"M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z\"></path></svg></a> Search results for \"{$_GET['mc-search_term']}\"</h4>";
        }
        ?>
        <div class="brz-staffLayout__container">
            <?php if (count($resultsPagination) > 0) { ?>
                <div class="brz-staffLayout__content">
                    <?php
                    foreach ($resultsPagination as $key => $item) {
                        echo "<article>";
                        if ($show_images && $item['photourl']) {
                            echo "<div class=\"brz-ministryBrands__item--media\">";
                            echo "<img src=\"{$item['photourl']}\" alt=\"\" />";

                            echo "<div class='brz-staffLayout_rollover' onclick=''>";
                            echo "<div class='brz-staffLayout_rollover_inner'>";
                            if ($detail_url) {
                                echo "<a href=\"{$detail_url}?mc-slug={$item['id']}\">";
                                echo "<span class='spacer'></span>";
                                echo "<p>";
                                echo "<span class=\"brz-staffLayout_rollover_inner--description\">$detail_page_button_text</span>";
                                echo "</p>";
                                echo "</a>";
                            }
                            echo "</div>";

                            echo "<ul class=\"brz-staffLayout_social brz-ministryBrands__item--meta-email\">";
                            if (!$item['customhideemail'] && !$show_full_email && $show_email && ($item['emailaddress'] || $item['altemailaddress'])) {
                                $item['emailaddress'] = $item['customdisplayemail'] ?: ($item['altemailaddress'] ?: $item['emailaddress']);
                                echo "<li><a href=\"mailto:{$item['emailaddress']}\" title=\"Email\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\"><path d=\"M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z\"/></svg></a></li>";
                            }
                            if ($show_facebook && $item['facebookurl']) {
                                echo "<li><a href=\"{$item['facebookurl']}\" title=\"Facebook\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 320 512\"><path d=\"M279.1 288l14.2-92.7h-88.9v-60.1c0-25.4 12.4-50.1 52.2-50.1h40.4V6.3S260.4 0 225.4 0c-73.2 0-121.1 44.4-121.1 124.7v70.6H22.9V288h81.4v224h100.2V288z\"/></svg></a></li>";
                            }
                            if ($show_twitter && $item['twitterurl']) {
                                echo "<li><a href=\"{$item['twitterurl']}\" title=\"Twitter\" target=\"_blank\"><svg xmlns='http://www.w3.org/2000/svg' fill='currentColor' class='brz-icon-svg align-[initial]' viewBox='0 0 16 16'><path d='M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z'/></svg></a></li>";
                            }
                            if ($show_instagram && $item['instagramurl']) {
                                echo "<li><a href=\"{$item['instagramurl']}\" title=\"Instagram\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path d=\"M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z\"/></svg></a></li>";
                            }
                            if ($show_website && $item['websiteurl']) {
                                echo "<li><a href=\"{$item['websiteurl']}\" title=\"Website\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\"><path d=\"M352 256c0 22.2-1.2 43.6-3.3 64H163.3c-2.2-20.4-3.3-41.8-3.3-64s1.2-43.6 3.3-64H348.7c2.2 20.4 3.3 41.8 3.3 64zm28.8-64H503.9c5.3 20.5 8.1 41.9 8.1 64s-2.8 43.5-8.1 64H380.8c2.1-20.6 3.2-42 3.2-64s-1.1-43.4-3.2-64zm112.6-32H376.7c-10-63.9-29.8-117.4-55.3-151.6c78.3 20.7 142 77.5 171.9 151.6zm-149.1 0H167.7c6.1-36.4 15.5-68.6 27-94.7c10.5-23.6 22.2-40.7 33.5-51.5C239.4 3.2 248.7 0 256 0s16.6 3.2 27.8 13.8c11.3 10.8 23 27.9 33.5 51.5c11.6 26 20.9 58.2 27 94.7zm-209 0H18.6C48.6 85.9 112.2 29.1 190.6 8.4C165.1 42.6 145.3 96.1 135.3 160zM8.1 192H131.2c-2.1 20.6-3.2 42-3.2 64s1.1 43.4 3.2 64H8.1C2.8 299.5 0 278.1 0 256s2.8-43.5 8.1-64zM194.7 446.6c-11.6-26-20.9-58.2-27-94.6H344.3c-6.1 36.4-15.5 68.6-27 94.6c-10.5 23.6-22.2 40.7-33.5 51.5C272.6 508.8 263.3 512 256 512s-16.6-3.2-27.8-13.8c-11.3-10.8-23-27.9-33.5-51.5zM135.3 352c10 63.9 29.8 117.4 55.3 151.6C112.2 482.9 48.6 426.1 18.6 352H135.3zm358.1 0c-30 74.1-93.6 130.9-171.9 151.6c25.5-34.2 45.2-87.7 55.3-151.6H493.4z\"/></svg></a></li>";
                            }

                            echo "</ul>";
                            echo "</div>";
                            echo "</div>";
                        }
                        echo "<div class=\"brz-staffLayout__item\">";

                        if ($show_title && $item['fullname']) {
                            echo "<h4 class=\"brz-staffLayout_heading brz-ministryBrands__item--meta-title\">";
                            if ($detail_url) {
                                echo "<a href=\"{$detail_url}?mc-slug={$item['id']}\">";
                            }
                            echo "{$item['fullname']}";
                            if ($detail_url) {
                                echo "</a>";
                            }
                            echo "</h4>";
                        }

                        if ($show_position && $item['position']) {
                            echo "<h6 class=\"brz-staffLayout_position brz-ministryBrands__item--meta-position\">{$item['position']}</h6>";
                        }

                        if ($show_groups && $item['groups']) {
                            echo "<p class=\"brz-staffLayout_meta brz-ministryBrands__item--meta-groups\">";
                            if ($show_meta_headings) {
                                if ($show_meta_icons) {
                                    echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 640 512\"><path fill=\"currentColor\" d=\"M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z\"></path></svg></span>";
                                } else {
                                    echo "<span>Groups: </span>";
                                }
                            }
                            echo "{$item['groups']}";
                            echo "</p>";
                        }

                        if (!$item['customhideemail'] && $show_full_email && ($item['emailaddress'] || $item['altemailaddress'])) {
                            $item['emailaddress'] = $item['customdisplayemail'] ?: ($item['altemailaddress'] ?: $item['emailaddress']);
                            echo "<p class=\"brz-staffLayout_meta brz-ministryBrands__item--meta-full-email\">";
                            if ($show_meta_headings) {
                                if ($show_meta_icons) {
                                    echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\"><path d=\"M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z\"/></svg></span>";
                                } else {
                                    echo "<span>Email: </span>";
                                }
                            }
                            echo "<a class='brz-staffLayout__link brz-staffLayout__link--full-email' href=\"mailto:{$item['emailaddress']}\" title=\"Email\">{$item['emailaddress']}</a>";
                            echo "</p>";
                        }

                        if ($show_phone_work && $item['workphone']) {
                            echo "<p class=\"brz-staffLayout_meta brz-ministryBrands__item--meta-workphone\">";
                            if ($show_meta_headings) {
                                if ($show_meta_icons) {
                                    echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 384 512\"><path d=\"M16 64C16 28.7 44.7 0 80 0H304c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H80c-35.3 0-64-28.7-64-64V64zM224 448a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM304 64H80V384H304V64z\"/></svg></span>";
                                } else {
                                    echo "<span>Phone: </span>";
                                }
                            }
                            echo "{$item['workphone']}";
                            echo "</p>";
                        }

                        if ($show_phone_cell && $item['cellphone']) {
                            echo "<p class=\"brz-staffLayout_meta brz-ministryBrands__item--meta-cellphone\">";
                            if ($show_meta_headings) {
                                if ($show_meta_icons) {
                                    echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 384 512\"><path d=\"M16 64C16 28.7 44.7 0 80 0H304c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H80c-35.3 0-64-28.7-64-64V64zM224 448a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM304 64H80V384H304V64z\"/></svg></span>";
                                } else {
                                    echo "<span>Cell: </span>";
                                }
                            }
                            echo "{$item['cellphone']}";
                            echo "</p>";
                        }

                        if (!$show_images) {
                            echo "<ul class=\"brz-staffLayout_social no-image\">";
                            if (!$item['customhideemail'] && !$show_full_email && $show_email && ($item['emailaddress'] || $item['altemailaddress'])) {
                                $item['emailaddress'] = $item['customdisplayemail'] ?: ($item['altemailaddress'] ?: $item['emailaddress']);
                                echo "<li><a href=\"mailto:{$item['emailaddress']}\" title=\"Email\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\"><path d=\"M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z\"/></svg></a></li>";
                            }
                            if ($show_facebook && $item['facebookurl']) {
                                echo "<li><a href=\"{$item['facebookurl']}\" title=\"Facebook\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 320 512\"><path d=\"M279.1 288l14.2-92.7h-88.9v-60.1c0-25.4 12.4-50.1 52.2-50.1h40.4V6.3S260.4 0 225.4 0c-73.2 0-121.1 44.4-121.1 124.7v70.6H22.9V288h81.4v224h100.2V288z\"/></svg></a></li>";
                            }
                            if ($show_twitter && $item['twitterurl']) {
                                echo "<li><a href=\"{$item['twitterurl']}\" title=\"Twitter\" target=\"_blank\"><svg xmlns='http://www.w3.org/2000/svg' fill='currentColor' class='brz-icon-svg align-[initial]' viewBox='0 0 16 16'><path d='M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z'/></svg></a></li>";
                            }
                            if ($show_instagram && $item['instagramurl']) {
                                echo "<li><a href=\"{$item['instagramurl']}\" title=\"Instagram\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path d=\"M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z\"/></svg></a></li>";
                            }
                            if ($show_website && $item['websiteurl']) {
                                echo "<li><a href=\"{$item['websiteurl']}\" title=\"Website\" target=\"_blank\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\"><path d=\"M352 256c0 22.2-1.2 43.6-3.3 64H163.3c-2.2-20.4-3.3-41.8-3.3-64s1.2-43.6 3.3-64H348.7c2.2 20.4 3.3 41.8 3.3 64zm28.8-64H503.9c5.3 20.5 8.1 41.9 8.1 64s-2.8 43.5-8.1 64H380.8c2.1-20.6 3.2-42 3.2-64s-1.1-43.4-3.2-64zm112.6-32H376.7c-10-63.9-29.8-117.4-55.3-151.6c78.3 20.7 142 77.5 171.9 151.6zm-149.1 0H167.7c6.1-36.4 15.5-68.6 27-94.7c10.5-23.6 22.2-40.7 33.5-51.5C239.4 3.2 248.7 0 256 0s16.6 3.2 27.8 13.8c11.3 10.8 23 27.9 33.5 51.5c11.6 26 20.9 58.2 27 94.7zm-209 0H18.6C48.6 85.9 112.2 29.1 190.6 8.4C165.1 42.6 145.3 96.1 135.3 160zM8.1 192H131.2c-2.1 20.6-3.2 42-3.2 64s1.1 43.4 3.2 64H8.1C2.8 299.5 0 278.1 0 256s2.8-43.5 8.1-64zM194.7 446.6c-11.6-26-20.9-58.2-27-94.6H344.3c-6.1 36.4-15.5 68.6-27 94.6c-10.5 23.6-22.2 40.7-33.5 51.5C272.6 508.8 263.3 512 256 512s-16.6-3.2-27.8-13.8c-11.3-10.8-23-27.9-33.5-51.5zM135.3 352c10 63.9 29.8 117.4 55.3 151.6C112.2 482.9 48.6 426.1 18.6 352H135.3zm358.1 0c-30 74.1-93.6 130.9-171.9 151.6c25.5-34.2 45.2-87.7 55.3-151.6H493.4z\"/></svg></a></li>";
                            }
                            echo "</ul>";
                        }
                        echo "</div>";
                        echo "</article>";
                    }
                    ?>
                </div>
            <?php
                if ($show_pagination){
                    echo '<p id="brz-staffLayout__pagination" class="brz-staffLayout__pagination">' . $pagination->getLinks($_GET, 'mc-page') . '</p>';
                }
            } else { ?>

                <p class="brz-staffLayout-no-results">There are no staff available.</p>

            <?php
            }
            ?>
        </div>
<?php
    }
}