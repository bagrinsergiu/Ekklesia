<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class EventFeaturedPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_event_featured';
    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'show_image'              => false,
            'show_title'              => false,
            'show_date'               => false,
            'show_category'           => false,
            'show_group'              => false,
            'show_meta_headings'      => false,
            'show_location'           => false,
            'show_room'               => false,
            'show_coordinator'        => false,
            'show_coordinator_email'  => false,
            'show_coordinator_phone'  => false,
            'show_cost'               => false,
            'show_website'            => false,
            'show_registration'       => false,
            'show_description'        => false,
            'show_preview'            => false,
            'show_latest_events'      => false,
            'recentEvents'            => '',
            'event_slug'              => false,
            'category'                => 'all',
            'group'                   => 'all',
            'features'                => '',
            'nonfeatures'             => '',
            'detail_page_button_text' => false,
            'detail_page'             => false,
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $cms          = $this->monkCMS;
        $recentEvents = $settings['recentEvents'] != '' ? $settings['recentEvents'] : '';
        $category     = $settings['category'] != 'all' ? $settings['category'] : '';
        $group        = $settings['group'] != 'all' ? $settings['group'] : '';
        $detail_url   = $detail_page ? $this->replacer->replacePlaceholders(urldecode($detail_page), $context) : false;
        $slugLink     = false;
        $slug         = false;

        if ($features) {
            $nonfeatures = '';
        } elseif ($nonfeatures) {
            $features = '';
        }

        //make content
        if ($show_latest_events) {
            $content1 = $cms->get([
                'module'        => 'event',
                'display'       => 'list',
                'order'         => 'recent',
                'howmany'       => 1,
                'find_category' => $category,
                'find_group'    => $group,
                'features'      => $features,
                'nonfeatures'   => $nonfeatures,
                'emailencode'   => 'no',
            ]);

            $content  = empty($content1['show'][0]) ? [] : $content1['show'][0];
            $slugLink = empty($content['slug']) ? '' : $content['slug'];

        } else {
            if ($event_slug) {
                $slug = $event_slug;
                $slugLink = $slug;
            } elseif ($recentEvents != '') {
                $slug = $recentEvents;
                $slugLink = $slug;
            }
        }

        if ($slug) {
            $content = $cms->get([
                'module'      => 'event',
                'display'     => 'detail',
                'find'        => $slug,
                'emailencode' => 'no',
            ])['show'];
        }
?>


        <?php //output
        if (isset($content) && count($content) > 0) {
            $item = $content;
        ?>
            <?php
            echo "<div class=\"brz-eventFeatured__item\">";
            if ($show_title) {
                echo "<h2 class=\"brz-eventFeatured__item--meta--title\">";
                if ($detail_url) echo "<a href=\"{$detail_url}?mc-slug={$slugLink}\">";
                echo "{$item['title']}";
                if ($detail_url) echo "</a>";
                echo "</h4>";
            }
            if ($show_date) {
                echo "<h5 class=\"brz-eventFeatured__item--meta--date\">{$item['eventtimes']}</h5>";
            }
            if ($show_image && $item['imageurl']) {
                if ($detail_url) echo "<a href=\"{$detail_url}?mc-slug={$slugLink}\">";
                echo "<div class=\"brz-ministryBrands__item--media\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                if ($detail_url) echo "</a>";
            }
            if ($show_category && $item['category']) {
                echo "<h6 class=\"brz-eventFeatured__item--meta\">";
                if ($show_meta_headings) echo "Category: ";
                echo "{$item['category']}";
                echo "</h6>";
            }
            if ($show_group && $item['group']) {
                echo "<h6 class=\"brz-eventFeatured__item--meta\">";
                if ($show_meta_headings) echo "Group: ";
                echo "{$item['group']}";
                echo "</h6>";
            }

            if ($show_location && $item['location']) {
                echo "<h6 class=\"brz-eventFeatured__item--meta\">Location: {$item['location']}</h6>";
                if ($item['fulladdress']) {
                    echo "<h6 class=\"brz-eventFeatured__item--meta--link\">";
                    if ($show_meta_headings) echo "<span class='brz-eventFeatured__item--meta'>Address: </span>";
                    echo "<a href=\"http://maps.google.com/maps?q={$item["fulladdress"]}\" target=\"_blank\">{$item['fulladdress']}</a>";
                    echo "</h6>";
                }
            }
            if ($show_room && $item['room']) {
                echo "<h6 class=\"brz-eventFeatured__item--meta\">";
                if ($show_meta_headings) echo "Room: ";
                echo "{$item['room']}";
                echo "</h6>";
            }
            if ($show_coordinator && $item['coordname']) {
                echo "<h6 class=\"brz-eventFeatured__item--meta\">";
                if ($show_meta_headings) echo "Coordinator: ";
                echo "{$item['coordname']}";
                echo "</h6>";
                if ($show_coordinator_email && $item['coordemail']) {
                    echo "<h6 class=\"brz-eventFeatured__item--meta--link\">";
                    if ($show_meta_headings) echo "<span class='brz-eventFeatured__item--meta'>Coordinator Email: </span>";
                    echo "<a href=\"mailto:{$item['coordemail']}\">{$item['coordemail']}</a>";
                    echo "</h6>";
                }
                if ($show_coordinator_phone && $item['coordphone']) {
                    echo "<h6 class=\"brz-eventFeatured__item--meta\">";
                    if ($show_meta_headings) echo "Coordinator Phone: ";
                    echo "{$item['coordphone']}";
                    echo "</h6>";
                }
            }
            if ($show_cost && $item['cost']) {
                echo "<h6 class=\"brz-eventFeatured__item--meta\">";
                if ($show_meta_headings) echo "Cost: ";
                echo "{$item['cost']}";
                echo "</h6>";
            }
            if ($show_website && $item['website']) {
                echo "<h6 class=\"brz-eventFeatured__item--meta--link\">";
                if ($show_meta_headings) echo "<span class='brz-eventFeatured__item--meta'>Website: </span>";
                echo "<a href=\"{$item['website']}\">{$item['website']}</a>";
                echo "</h6>";
            }
            if ($show_registration && $item['registrationurl']) {
                echo "<div class=\"brz-ministryBrands__item--meta--register-button\"><a href=\"{$item['registrationurl']}\" target=\"_blank\">Register</a></div>";
            }
            if ($show_registration && $item['externalregistrationurl']) {
                echo "<div class=\"brz-ministryBrands__item--meta--register-button\"><a href=\"{$item['externalregistrationurl']}\" target=\"_blank\">Register</a></div>";
            }
            if ($show_preview && $item['preview']) {
                $item['preview'] = substr($item['preview'], 0, 110) . " ...";
                echo "<p class=\"brz-eventFeatured__item--meta--preview\"><span>{$item['preview']}</span></p>";
            }
            if ($show_description && $item['text']) {
                echo "<div class=\"brz-eventFeatured__item--meta--preview\">{$item['text']}</div>";
            }

            if ($detail_url && $detail_page_button_text) {
                echo "<p class=\"brz-ministryBrands__item--meta--button\"><a href=\"{$detail_url}?mc-slug={$slugLink}\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">{$detail_page_button_text}</span></a></p>";
            }

            echo "</div>";
            ?>
        <?php
        } //no output
        else {
        ?>
            <p>There is no event available.</p>

        <?php
        }
        ?>
<?php
    }
}
