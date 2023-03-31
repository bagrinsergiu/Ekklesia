<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class EventDetailPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_event_detail';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'show_image'             => false,
            'show_title'             => false,
            'show_date'              => false,
            'show_category'          => false,
            'show_group'             => false,
            'show_meta_headings'     => false,
            'show_location'          => false,
            'show_room'              => false,
            'show_coordinator'       => false,
            'show_coordinator_email' => false,
            'show_coordinator_phone' => false,
            'show_cost'              => false,
            'show_website'           => false,
            'show_registration'      => false,
            'show_description'       => false,
            'events_recent'          => false,
            'previous_page'          => false
        ];

        $settings = array_merge($options, $placeholder->getAttributes());
        $cms      = $this->monkCMS;

        extract($settings);
        
        if (isset($_GET['ekk-slug'])) {
            $slug = $_GET['ekk-slug'];
        } elseif ($events_recent) {
            $slug = $events_recent;
        } else {
            $recent = $cms->get([
                'module'      => 'event',
                'display'     => 'list',
                'order'       => 'recent',
                'emailencode' => 'no',
                'howmany'     => 1
            ]);
            
            $slug = !empty($recent['show'][0]['slug']) ? $recent['show'][0]['slug'] : '';
        }

        $content = $cms->get([
            'module'      => 'event',
            'display'     => 'detail',
            'emailencode' => 'no',
            'find'        => $slug,
        ]);

        if ( isset($content['show']) && count($content['show']) > 0) {
            $item = $content['show'];
            ?>

            <div class="brz-eventDetail__item">
                <?php
                if ($show_title) {
                    echo "<h2 class=\"brz-eventDetail__item--meta--title\">{$item['title']}</h2>";
                }
                if ($show_date) {
                    echo "<h5 class=\"brz-eventDetail__item--meta--date\">{$item['eventtimes']}</h5>";
                }
                if ($show_image && $item['imageurl']) {
                    echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                }
                if ($show_category && $item['category']) {
                    echo "<h6 class=\"brz-eventDetail__item--meta\">";
                    if ($show_meta_headings) echo "Category: ";
                    echo "{$item['category']}";
                    echo "</h6>";
                }
                if ($show_group && $item['group']) {
                    echo "<h6 class=\"brz-eventDetail__item--meta\">";
                    if ($show_meta_headings) echo "Group: ";
                    echo "{$item['group']}";
                    echo "</h6>";
                }

                if ($show_location && $item['location']) {
                    echo "<h6 class=\"brz-eventDetail__item--meta\">";
                    if ($show_meta_headings) echo "Location: ";
                    echo "{$item['location']}";
                    echo "</h6>";
                    if ($item['fulladdress']) {
                        echo "<h6 class=\"brz-eventDetail__item--meta--container\">";
                        if ($show_meta_headings) echo "<span class='brz-eventDetail__item--meta'>Address: </span>";
                        echo "<a class='brz-ministryBrands__item--meta--links' href=\"http://maps.google.com/maps?q={$item["fulladdress"]}\" target=\"_blank\">{$item['fulladdress']}</a>";
                        echo "</h6>";
                    }
                }
                if ($show_room && $item['room']) {
                    echo "<h6 class=\"brz-eventDetail__item--meta\">";
                    if ($show_meta_headings) echo "Room: ";
                    echo "{$item['room']}";
                    echo "</h6>";
                }
                if ($show_coordinator && $item['coordname']) {
                    echo "<h6 class=\"brz-eventDetail__item--meta\">";
                    if ($show_meta_headings) echo "Coordinator: ";
                    echo "{$item['coordname']}";
                    echo "</h6>";
                    if ($show_coordinator_email && $item['coordemail']) {
                        echo "<h6 class=\"brz-eventDetail__item--meta--link\">";
                        if ($show_meta_headings) echo "<span class='brz-eventDetail__item--meta' >Coordinator Email: </span>";
                        echo "<a href=\"mailto:{$item['coordemail']}\">{$item['coordemail']}</a>";
                        echo "</h6>";
                    }
                    if ($show_coordinator_phone && $item['coordphone']) {
                        echo "<h6 class=\"brz-eventDetail__item--meta\">";
                        if ($show_meta_headings) echo "Coordinator Phone: ";
                        echo "{$item['coordphone']}";
                        echo "</h6>";
                    }
                }
                if ($show_cost && $item['cost']) {
                    echo "<h6 class=\"brz-eventDetail__item--meta\">";
                    if ($show_meta_headings) echo "Cost: ";
                    echo "{$item['cost']}";
                    echo "</h6>";
                }
                if ($show_website && $item['website']) {
                    echo "<h6 class=\"brz-eventDetail__item--meta--container\">";
                    if ($show_meta_headings) echo "<span class='brz-eventDetail__item--meta'>Website: </span>";
                    echo "<a class='brz-ministryBrands__item--meta--links' href=\"{$item['website']}\">{$item['website']}</a>";
                    echo "</h6>";
                }
                if ($show_registration && $item['registrationurl']) {
                    echo "<div class=\"brz-ministryBrands__item--meta--button\"><a href=\"{$item['registrationurl']}\" target=\"_blank\">Register</a></div>";
                }
                if ($show_registration && $item['externalregistrationurl']) {
                    echo "<div class=\"brz-ministryBrands__item--meta--button\"><a href=\"{$item['externalregistrationurl']}\" target=\"_blank\">Register</a></div>";
                }
                if ($show_description && $item['text']) {
                    echo "<div class=\"brz-eventDetail__item--meta--preview\">{$item['text']}</div>";
                }

                if ($previous_page) {
                    echo '<div class="brz-ministryBrands__item--meta--links brz-ministryBrands__item--meta--links--previous">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" class="brz-icon-svg align-[initial]"><path d="M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z"></path></svg>
                    Previous Page</div>';
                }
                ?>
            </div>
            <?php
        } else {
            ?>
            <p>There is no event available.</p>
            <?php
        }
    }
}
