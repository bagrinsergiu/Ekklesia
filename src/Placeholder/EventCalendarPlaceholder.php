<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;
use DateInterval;
use DatePeriod;
use DateTime;

class EventCalendarPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_event_calendar';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $options = [
            'category'      => 'all',
            'group'         => 'all',
            'features'      => '',
            'nonfeatures'   => '',
            'detail_page'   => false,
            'howmanymonths' => 3,
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $cms           = $this->monkCMS;
        $category      = $settings['category'] != 'all' ? $settings['category'] : '';
        $group         = $settings['group'] != 'all' ? $settings['group'] : '';
        $detail_url    = $settings['detail_page'] ? home_url($settings['detail_page']) : false;
        $calendarStart = date('Y-m-d');
        $calendarEnd   = date('Y-m-d', strtotime("+{$howmanymonths} months"));
        $date1         = new DateTime($calendarStart);
        $date2         = new DateTime($calendarEnd);
        $diff          = $date1->diff($date2, true);
        $calendarDays  = $diff->format('%a');

        if ($features) {
            $nonfeatures = '';
        } elseif ($nonfeatures) {
            $features = '';
        }

        $content = $cms->get([
            'module'        => 'event',
            'display'       => 'list',
            'emailencode'   => 'no',
            'recurring'     => 'yes',
            'repeatevent'   => 'yes',
            'groupby'       => 'day',
            'howmanydays'   => $calendarDays,
            'find_category' => $category,
            'find_group'    => $group,
            'features'      => $features,
            'nonfeatures'   => $nonfeatures
        ]);
        ?>

        <div class="ekklesia360_event_calendar_wrap">

            <?php //output
            if (count($content['show']) > 0) {
                //iterate over each event and assign to month and day
                foreach ($content["show"] as $show) {
                    $grouping_month = date("Y-m", strtotime($show["eventstart"]));
                    $grouping_day = date("Y-m-d", strtotime($show["eventstart"]));
                    $events[$grouping_month][$grouping_day][] = $show;//set first dimension to day and then assign all events as second level to that day
                }
                ?>

                <div class="ekklesia360_event_calendar">
                    <?php
                    echo self::draw_calendar($events, $detail_url);
                    ?>
                </div>
                <?php
            } else {
                ?>

                <p>There are no events available.</p>

                <?php
            }
            ?>
        </div>
        <?php
    }

    //draw
    public function draw_calendar($events = null, $detail_url = null)
    {
        $results = false;
        $period = self::get_period($events);

        $period_arr = iterator_to_array($period);

        $start_month = reset($period_arr);
        $start_month_format = $start_month->format("Y-m");

        $end_month = end($period_arr);
        $end_month_format = $end_month->format("Y-m");

        //iterate each month
        foreach ($period as $month) {
            //set month formats
            $month_format = $month->format("Y-m");//should match format of initial $events month
            $month_format_month = $month->format("m");//month to draw table
            $month_format_year = $month->format("Y");//month to draw table

            $month_label_format = $month->format("F Y");

            //month pagination set
            $pag_format = $month->format("m-Y");
            $prev_month = date("Y-m", strtotime("1-{$pag_format} -1 month"));
            $next_month = date("Y-m", strtotime("1-{$pag_format} +1 month"));


            //open .month div
            $results .= "<div class=\"month-view {$month_format}\">";

            //pagination
            $results .= "<div class=\"ekklesia360_layout-pagination\">";
            //prev
            if ($month_format === $start_month_format) {
                $results .= "<a class=\"previous off\"><i class=\"fas fa-angle-left\"></i></a>";
            } else {
                $results .= "<a href=\"{$prev_month}\" data-month=\"{$prev_month}\" class=\"previous\"><i class=\"fas fa-angle-left\"></i></a>";
            }
            //heading
            $results .= "<span class=\"heading\">{$month_label_format}</span>";

            //next
            if ($month_format === $end_month_format) {
                $results .= "<a class=\"next off\"><i class=\"fas fa-angle-right\"></i></a>";
            } else {
                $results .= "<a href=\"{$next_month}\" data-month=\"{$next_month}\" class=\"next\"><i class=\"fas fa-angle-right\"></i></a>";
            }

            $results .= "</div>";


            //if no output nrf
            if (count($events[$month_format]) < 1) {
                $results .= "<h4 class=\"nrf\">There are no events for this month.</h4>";
            } //else results
            else {
                //get table of month and pass/format events
                $results .= self::draw_calendar_table($month_format_month, $month_format_year, $events[$month_format], $detail_url);

            }//end if

            //close .month div
            $results .= "</div>";

        }//end foreach month


        return $results;
    }

    //draws calendar table
    private function draw_calendar_table($month, $year, $events = null, $detail_url = null)
    {

        /* draw table */
        $calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

        /* table headings */
        $headings = array('Sun', 'Mon', 'Tues', 'Wed', 'Thu', 'Fri', 'Sat');

        $calendar .= '<tr class="calendar-row weekdays"><th>' . implode('</th><th>', $headings) . '</th></tr>';

        /* days and weeks vars now ... */
        $running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
        $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
        $days_in_this_week = 1;
        $day_counter = 0;
        $dates_array = array();

        /* row for week one */
        $calendar .= '<tr class="calendar-row">';

        /* print "blank" days until the first of the current week */
        for ($x = 0; $x < $running_day; $x++):
            $calendar .= '<td class="calendar-day-np"> </td>';
            $days_in_this_week++;
        endfor;

        /* keep going with days.... */
        for ($list_day = 1; $list_day <= $days_in_month; $list_day++):

            $cur_date = date('Y-m-d', mktime(0, 0, 0, $month, $list_day, $year));

            $calendar .= '<td class="calendar-day">';
            /* add in the day number */
            $calendar .= '<div class="day-number"><span>' . $list_day . '</span></div>';

            /** QUERY FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
            //$calendar.= str_repeat('<p> </p>',2);
            if (isset($events) && isset($events[$cur_date])) {
                //print_r($events[$cur_date]);
                $calendar .= "<ul class=\"calendar-links\">";
                foreach ($events[$cur_date] as $v) {
                    if ($detail_url) {
                        $v["url"] = str_replace('/event/', "{$detail_url}?ekklesia360_event_slug=", $v['url']);
                    }
                    $calendar .= "<li>";
                    $calendar .= "<span class=\"title\">";
                    if ($detail_url) $calendar .= "<a href=\"{$v['url']}\" title=\"{$v["title"]}\">";
                    $calendar .= "{$v["title"]}";
                    if ($detail_url) $calendar .= "</a>";
                    $calendar .= "</span>";
                    $calendar .= "</li>";
                }
                $calendar .= "<ul>";
            }

            $calendar .= '</td>';
            if ($running_day == 6):
                $calendar .= '</tr>';
                if (($day_counter + 1) != $days_in_month):
                    $calendar .= '<tr class="calendar-row">';
                endif;
                $running_day = -1;
                $days_in_this_week = 0;
            endif;
            $days_in_this_week++;
            $running_day++;
            $day_counter++;
        endfor;

        /* finish the rest of the days in the week */
        if ($days_in_this_week < 8):
            for ($x = 1; $x <= (8 - $days_in_this_week); $x++):
                $calendar .= '<td class="calendar-day-np"> </td>';
            endfor;
        endif;

        /* final row */
        $calendar .= '</tr>';

        /* end the table */
        $calendar .= '</table>';

        /* all done, return result */
        return $calendar;
    }

    //get period between two months
    private function get_period($events)
    {
        $last = key(end($events));
        $first = key(reset($events));
        $start = (new DateTime($first))->modify('first day of this month');
        $end = (new DateTime($last))->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);
        return $period;
    }
}