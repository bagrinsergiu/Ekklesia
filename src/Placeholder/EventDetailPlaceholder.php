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
            'show_image'             => true,
            'show_title'             => true,
            'show_date'              => true,
            'show_category'          => true,
            'show_group'             => true,
            'show_meta_headings'     => true,
            'show_location'          => true,
            'show_room'              => true,
            'show_coordinator'       => true,
            'show_coordinator_email' => false,
            'show_coordinator_phone' => false,
            'show_cost'              => true,
            'show_website'           => false,
            'show_registration'      => true,
            'show_description'       => true,
            'events_recent'          => false,
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $cms    = $this->monkCMS;
        $recent = $cms->get([
            'module'      => 'event',
            'display'     => 'list',
            'order'       => 'recent',
            'emailencode' => 'no',
            'howmany'     => 1
        ]);

        //make slug...would be from widget-event-list.php
        if (isset($_GET['ekklesia360_event_slug'])) {
            $slug = $_GET['ekklesia360_event_slug'];
        } elseif ($events_recent) {
            $slug = $events_recent;
        } else {
            $slug = !empty($recent['show'][0]['slug']) ? $recent['show'][0]['slug'] : '';
        }

        $content = $cms->get([
            'module'      => 'event',
            'display'     => 'detail',
            'emailencode' => 'no',
            'find'        => $slug,
        ]);
        ?>

        <div class="ekklesia360_event_detail_wrap">

            <?php //output
            if (count($content['show']) > 0) {
                $item = $content['show'];
                ?>

                <div class="ekklesia360_event_detail">
                    <?php
                    echo "<article>";
                    echo "<div class=\"info\">";
                    if ($show_title) {
                        echo "<h2 class=\"ekklesia360_event_detail_heading\">{$item['title']}</h2>";
                    }
                    if ($show_date) {
                        echo "<h5 class=\"ekklesia360_event_detail_times\">{$item['eventtimes']}</h5>";
                    }
                    if ($show_image && $item['imageurl']) {
                        echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                    }
                    if ($show_category && $item['category']) {
                        echo "<h6 class=\"ekklesia360_event_detail_meta\">";
                        if ($show_meta_headings) echo "Category: ";
                        echo "{$item['category']}";
                        echo "</h6>";
                    }
                    if ($show_group && $item['group']) {
                        echo "<h6 class=\"ekklesia360_event_detail_meta\">";
                        if ($show_meta_headings) echo "Group: ";
                        echo "{$item['group']}";
                        echo "</h6>";
                    }

                    if ($show_location && $item['location']) {
                        echo "<h6 class=\"ekklesia360_event_detail_meta\">";
                        if ($show_meta_headings) echo "Location: ";
                        echo "{$item['location']}";
                        echo "</h6>";
                        if ($item['fulladdress']) {
                            echo "<h6 class=\"ekklesia360_event_detail_meta\">";
                            if ($show_meta_headings) echo "Address: ";
                            echo "<a href=\"http://maps.google.com/maps?q={$item["fulladdress"]}\" target=\"_blank\">{$item['fulladdress']}</a>";
                            echo "</h6>";
                        }
                    }
                    if ($show_room && $item['room']) {
                        echo "<h6 class=\"ekklesia360_event_detail_meta\">";
                        if ($show_meta_headings) echo "Room: ";
                        echo "{$item['room']}";
                        echo "</h6>";
                    }
                    if ($show_coordinator && $item['coordname']) {
                        echo "<h6 class=\"ekklesia360_event_detail_meta\">";
                        if ($show_meta_headings) echo "Coordinator: ";
                        echo "{$item['coordname']}";
                        echo "</h6>";
                        if ($show_coordinator_email && $item['coordemail']) {
                            echo "<h6 class=\"ekklesia360_event_detail_meta\">";
                            if ($show_meta_headings) echo "Coordinator Email: ";
                            echo "<a href=\"mailto:{$item['coordemail']}\">{$item['coordemail']}</a>";
                            echo "</h6>";
                        }
                        if ($show_coordinator_phone && $item['coordphone']) {
                            echo "<h6 class=\"ekklesia360_event_detail_meta\">";
                            if ($show_meta_headings) echo "Coordinator Phone: ";
                            echo "{$item['coordphone']}";
                            echo "</h6>";
                        }
                    }
                    if ($show_cost && $item['cost']) {
                        echo "<h6 class=\"ekklesia360_event_detail_meta\">";
                        if ($show_meta_headings) echo "Cost: ";
                        echo "{$item['cost']}";
                        echo "</h6>";
                    }
                    if ($show_website && $item['website']) {
                        echo "<h6 class=\"ekklesia360_event_detail_meta\">";
                        if ($show_meta_headings) echo "Website: ";
                        echo "<a href=\"{$item['website']}\">{$item['website']}</a>";
                        echo "</h6>";
                    }
                    if ($show_registration && $item['registrationurl']) {
                        echo "<p class=\"ekklesia360_event_detail_meta\"><a href=\"{$item['registrationurl']}\" target=\"_blank\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">Register</span></a></p>";
                    }
                    if ($show_registration && $item['externalregistrationurl']) {
                        echo "<p class=\"ekklesia360_event_detail_meta\"><a href=\"{$item['externalregistrationurl']}\" target=\"_blank\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">Register</span></a></p>";
                    }
                    if ($show_description && $item['text']) {
                        echo "<div class=\"ekklesia360_event_detail_content\">{$item['text']}</div>";
                    }

                    echo "<p class=\"ekklesia360_event_detail_previous\"><a href=\"javascript:history.back();\"><i class=\"fas fa-angle-left\"></i> Previous Page</a></p>";

                    echo "</div>";
                    echo "</article>";
                    ?>
                </div>
                <?php
            } //no output
            else {
                ?>

                <p>There is no event available.</p>

                <?php
            }
            ?>
        </div>
        <?php
    }
}