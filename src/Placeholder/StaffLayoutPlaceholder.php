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
            'detail_page_button_text' => 'Read More',
            'detail_page'             => '',
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $baseURL     = strtok($_SERVER['REQUEST_URI'], '?') !== false ? strtok($_SERVER['REQUEST_URI'], '?') : $_SERVER['REQUEST_URI'];
        $detail_url  = $detail_page ? $this->replacer->replacePlaceholders(urldecode($detail_page), $context) : '';
        $cms         = $this->monkCMS;
        $filterCount = count(array_filter([$show_group_filter]));
        $groups      = $show_group_filter ? $cms->get(['module' => 'group', 'display' => 'list',]) : [];

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

        ?>

        <div id="ekklesia360_staff_layout_filters" class="ekklesia360_staff_layout_filters">
            <form id="ekklesia360_staff_layout_form" name="ekklesia360_staff_layout_form" action="<?= $baseURL ?>" data-count="<?= $filterCount ?>">
                <?php if ($show_group_filter && !empty($groups['show'])): ?>
                    <select name="mc-group">
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
                <?php endif; ?>
            </form>

            <?php if ($show_search): ?>
                <form method="get" id="ekklesia360_staff_layout_search" name="mc-search" action="<?= $baseURL ?>" data-count="<?= $filterCount ?>">
                    <fieldset>
                        <input type="text" id="ekklesia360_staff_layout_search_term" name="mc-search_term" value="" placeholder="Search"/>
                        <button type="submit" name="submit" id="ekklesia360_staff_layout_search_submit" value=""><i class="fas fa-search"></i></button>
                    </fieldset>
                </form>
            <?php endif; ?>
        </div>

        <?php
        if (isset($_GET['mc-search_term'])) {
            echo "<h4 class=\"ekklesia360_staff_layout_results_heading\"><a href=\"{$baseURL}\"><i class=\"fas fa-times\"></i></a> Search results for \"{$_GET['mc-search_term']}\"</h4>";
        }
        ?>
        <div class="ekklesia360_staff_layout_wrap">
            <?php if (!empty($content['show'])) { ?>
                <div class="ekklesia360_staff_layout">
                    <?php
                    foreach ($content['show'] as $item) {
                        echo "<article>";
                        if ($show_images && $item['photourl']) {
                            echo "<div class=\"image\">";
                            echo "<img src=\"{$item['photourl']}\" alt=\"\" />";

                            echo "<div class='ekklesia360_staff_layout_rollover' onclick=''>";
                            echo "<div class='ekklesia360_staff_layout_rollover_inner'>";
                            if ($detail_url) {
                                echo "<a href=\"{$detail_url}?mc-slug={$item['id']}\">";
                                echo "<span class='spacer'></span>";
                                echo "<p>";
                                echo "<span>$detail_page_button_text</span>";
                                echo "</p>";
                                echo "</a>";
                            }
                            echo "</div>";

                            echo "<ul class=\"ekklesia360_staff_layout_social\">";
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

                            echo "</ul>";
                            echo "</div>";
                            echo "</div>";
                        }
                        echo "<div class=\"info\">";

                        if ($show_title) {
                            echo "<h4 class=\"ekklesia360_staff_layout_heading\">";
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
                            echo "<h6 class=\"ekklesia360_staff_layout_position\">{$item['position']}</h6>";
                        }

                        if ($show_groups && $item['groups']) {
                            echo "<p class=\"ekklesia360_staff_layout_meta\">";
                            if ($show_meta_headings) {
                                echo "Groups: ";
                            }
                            echo "{$item['groups']}";
                            echo "</p>";
                        }

                        if ($show_phone_work && $item['workphone']) {
                            echo "<p class=\"ekklesia360_staff_layout_meta\">";
                            if ($show_meta_headings) {
                                echo "Phone: ";
                            }
                            echo "{$item['workphone']}";
                            echo "</p>";
                        }

                        if ($show_phone_cell && $item['cellphone']) {
                            echo "<p class=\"ekklesia360_staff_layout_meta\">";
                            if ($show_meta_headings) {
                                echo "Cell: ";
                            }
                            echo "{$item['cellphone']}";
                            echo "</p>";
                        }

                        if (!$show_images) {
                            echo "<ul class=\"ekklesia360_staff_layout_social no-image\">";
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
                            echo "</ul>";
                        }
                        echo "</div>";
                        echo "</article>";
                    }
                    ?>
                </div>
            <?php } else { ?>

                <p>There are no staff available.</p>

                <?php
            }
            ?>
        </div>
        <?php
    }
}