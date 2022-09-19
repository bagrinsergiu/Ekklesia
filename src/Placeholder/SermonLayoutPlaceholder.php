<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class SermonLayoutPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_sermon_layout';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $baseURL = (strtok($_SERVER["REQUEST_URI"],'?') !== FALSE) ? strtok($_SERVER["REQUEST_URI"],'?') : $_SERVER["REQUEST_URI"];
        $options = [
            'show_group_filter'       => false,
            'group_filter_heading'    => 'Group',
            'show_category_filter'    => false,
            'category_filter_heading' => 'Category',
            'show_series_filter'      => false,
            'series_filter_heading'   => 'Series',
            'show_speaker_filter'     => false,
            'speaker_filter_heading'  => 'Speaker',
            'show_search'             => false,
            'search_placeholder'      => 'Search',
            // Constent Settings
            'column_count'            => 3,
            'column_count_tablet'     => 2,
            'column_count_mobile'     => 1,
            'show_pagination'         => false,
            'show_images'             => true,
            'show_video'              => false,
            'show_audio'              => true,
            'show_inline_video'       => true,
            'show_inline_audio'       => true,
            'show_media_links'        => true,
            'show_title'              => true,
            'show_date'               => true,
            'show_category'           => true,
            'show_group'              => true,
            'show_series'             => true,
            'show_preacher'           => true,
            'show_passage'            => true,
            'show_meta_headings'      => false,
            'show_preview'            => false,
            'detail_page_button_text' => false,
            'howmany'                 => 9,
            'sticky_space'            => 0,
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        extract($settings);

        $filterCountArr  = array($show_group_filter, $show_category_filter, $show_series_filter, $show_speaker_filter);
        $filterCount     = count(array_filter($filterCountArr));
        $detail_url      = !empty($settings['detail_page']) ? home_url($settings['detail_page']) : false;
        $cms             = $this->monkCMS;
        $page            = isset($_GET['ekklesia360_sermon_layout_page']) ? $_GET['ekklesia360_sermon_layout_page'] : 1;
        $category_filter = isset($_GET['category']) ? $_GET['category'] : false;
        $group_filter    = isset($_GET['group']) ? $_GET['group'] : false;
        $series_filter   = isset($_GET['series']) ? $_GET['series'] : false;
        $speaker_filter  = isset($_GET['speaker']) ? $_GET['speaker'] : false;

        $categories = $cms->get(array(
            'module'  => 'sermon',
            'display' => 'list',
            'groupby' => 'category'
        ));

        $groups = $cms->get(array(
            'module'  => 'sermon',
            'display' => 'list',
            'groupby' => 'group'
        ));

        $series = $cms->get(array(
            'module'  => 'sermon',
            'display' => 'list',
            'groupby' => 'series'
        ));

        $speakers = $cms->get(array(
            'module'  => 'sermon',
            'display' => 'list',
            'groupby' => 'preacher'
        ));

        //test search first
        if (isset($_GET['search_term'])) {
            $content = array();
            $search_arr = $cms->get(array(
                'module'  => 'search',
                'display' => 'results',
                'howmany' => '100',
                'keywords' => $_GET['search_term'],
                'find_module' => 'sermon',
                'hide_module' => 'media',
            ));
            foreach($search_arr['show'] as $search){
                $item = $cms->get(array(
                    'module'  => 'sermon',
                    'display' => 'detail',
                    'emailencode' => 'no',
                    'find' => $search['slug'],
                    'show' => "__videoplayer fullscreen='true'__",
                    'show' => "__audioplayer__",
                ));
                $content['show'][] = $item['show'];
            }
        }
        //if no search module api
        else
        {
            $content = $cms->get(array(
                'module'  => 'sermon',
                'display' => 'list',
                'order' => 'recent',
                'emailencode' => 'no',
                'howmany' => '1000',
                'find_category' => $category_filter,
                'find_group' => $group_filter,
                'find_series' => $series_filter,
                'find_preacher' => $speaker_filter,
                'show' => "__videoplayer fullscreen='true'__",
                'show' => "__audioplayer__",
            ));
        }

        ?>

        <div id="ekklesia360_sermon_layout_filters" class="ekklesia360_sermon_layout_filters">
            <form id="ekklesia360_sermon_layout_form" name="ekklesia360_sermon_layout_form" action="<?= $baseURL ?>" data-count="<?= $filterCount ?>">

                <?php if($show_group_filter && count($groups['group_show']) > 0): ?>
                    <select name="group" class='sorter' onchange='filterEkklesia360Sermons()'>
                        <option value=""><?= $group_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        foreach($groups['group_show'] as $group)
                        {
                            echo "<option value=\"{$group['slug']}\"";
                            if(isset($_GET['group']) && $_GET['group'] == $group['slug'])
                            {
                                echo " selected";
                            }
                            echo ">{$group['title']}</option>";
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <?php if($show_category_filter && count($categories['group_show']) > 0): ?>
                    <select name="category" class='sorter' onchange='filterEkklesia360Sermons()'>
                        <option value=""><?= $category_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        foreach($categories['group_show'] as $category)
                        {
                            echo "<option value=\"{$category['slug']}\"";
                            if(isset($_GET['category']) && $_GET['category'] == $category['slug'])
                            {
                                echo " selected";
                            }
                            echo ">{$category['title']}</option>";
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <?php if($show_series_filter && count($series['group_show']) > 0): ?>
                    <select name="series" class='sorter' onchange='filterEkklesia360Sermons()'>
                        <option value=""><?= $series_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        foreach($series['group_show'] as $serie)
                        {
                            echo "<option value=\"{$serie['slug']}\"";
                            if(isset($_GET['series']) && $_GET['series'] == $serie['slug'])
                            {
                                echo " selected";
                            }
                            echo ">{$serie['title']}</option>";
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <?php if($show_speaker_filter && count($speakers['group_show']) > 0): ?>
                    <select name="speaker" class='sorter' onchange='filterEkklesia360Sermons()'>
                        <option value=""><?= $speaker_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        foreach($speakers['group_show'] as $speaker)
                        {
                            echo "<option value=\"{$speaker['slug']}\"";
                            if(isset($_GET['speaker']) && $_GET['speaker'] == $speaker['slug'])
                            {
                                echo " selected";
                            }
                            echo ">{$speaker['title']}</option>";
                        }
                        ?>
                    </select>
                <?php endif; ?>

            </form>

            <?php if($show_search): ?>
                <form method="get" id="ekklesia360_sermon_layout_search" name="ekklesia360_sermon_layout_search" action="<?= $baseURL ?>" data-count="<?= $filterCount ?>">
                    <fieldset>
                        <input type="text" id="ekklesia360_sermon_layout_search_term" name="search_term" value="" placeholder="<?= $search_placeholder ?>" />
                        <button type="submit" name="submit" id="ekklesia360_sermon_layout_search_submit" value=""><i class="fas fa-search"></i></button>
                    </fieldset>
                </form>
            <?php endif; ?>
        </div>

        <?php if(isset($_GET['search_term']))
    {
        echo "<h4 class=\"ekklesia360_sermon_layout_results_heading\"><a href=\"{$baseURL}\"><i class=\"fas fa-times\"></i></a> Search results for \"{$_GET['search_term']}\"</h4>";
    }
        ?>

        <div id="ekklesia360_sermon_layout_wrap" class="ekklesia360_sermon_layout_wrap">

            <?php

            //setup pagination
            $pagination = new CustomPagination($content["show"], (isset($page) ? $page : 1), $howmany);
            $pagination->setShowFirstAndLast(true);
            $resultsPagination = $pagination->getResults();
            //output

            //output
            if(count($resultsPagination) > 0)
            {
                ?>

                <div class="ekklesia360_sermon_layout" data-columncount="<?php echo $column_count; ?>" data-columncount-tablet="<?php echo $column_count_tablet; ?>" data-columncount-mobile="<?php echo $column_count_mobile; ?>">
                    <?php
                    foreach($resultsPagination as $key => $item)
                    {
                        echo "<article>";
                        echo "<div class=\"info\">";

                        if( $show_images && $item['imageurl'] && !$show_video)
                        {
                            if($detail_url) echo "<a href=\"{$detail_url}?ekklesia360_sermon_slug={$item['slug']}\">";
                            echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                            if($detail_url) echo "</a>";
                        }
                        if( $show_video )
                        {
                            if($item['videoembed'])
                            {
                                echo "<div class=\"ekklesia360_sermon_media_responsive video\">{$item['videoembed']}</div>";
                            }
                            elseif($item['videourl'])
                            {
                                $videoext = pathinfo($item['videourl'], PATHINFO_EXTENSION);
                                echo "<div class=\"ekklesia360_sermon_media_responsive\">";
                                echo "<video src=\"{$item['videourl']}\" controls preload=\"none\" width=\"1024\" height=\"576\" poster=\"{$item['imageurl']}\" type=\"video/{$videoext}\"><p>The Video could not be played. Please <a href=\"{$item['videourl']}\" target=\"_blank\">download it here</a>.</p></video>";
                                echo "</div>";
                            }
                            elseif($show_image && $item['imageurl'])
                            {
                                echo "<div class=\"image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                            }
                        }
                        if($show_audio && $item['audiourl'] )
                        {
                            echo "<div class=\"ekklesia360_sermon_media_audio\">";
                            echo "<audio src=\"{$item['audiourl']}\" controls preload=\"none\"></audio>";
                            echo "</div>";
                        }

                        if($show_title )
                        {
                            echo "<h4 class=\"ekklesia360_sermon_layout_heading\">";
                            if($detail_url) echo "<a href=\"{$detail_url}?ekklesia360_sermon_slug={$item['slug']}\">";
                            echo "{$item['title']}";
                            if($detail_url) echo "</a>";
                            echo "</h4>";
                        }

                        if($show_date && $item['date'])
                        {
                            echo "<h6 class=\"ekklesia360_sermon_layout_meta\">";
                            if($show_meta_headings) echo "Date: ";
                            echo "{$item['date']}";
                            echo "</h6>";
                        }
                        if($show_category && $item['category'])
                        {
                            echo "<h6 class=\"ekklesia360_sermon_layout_meta\">";
                            if($show_meta_headings) echo "Category: ";
                            echo "{$item['category']}";
                            echo "</h6>";
                        }
                        if($show_group && $item['group'])
                        {
                            echo "<h6 class=\"ekklesia360_sermon_layout_meta\">";
                            if($show_meta_headings) echo "Group: ";
                            echo "{$item['group']}";
                            echo "</h6>";
                        }
                        if($show_series && $item['series'])
                        {
                            echo "<h6 class=\"ekklesia360_sermon_layout_meta\">";
                            if($show_meta_headings) echo "Series: ";
                            echo "{$item['series']}";
                            echo "</h6>";
                        }
                        if($show_preacher && $item['preacher'])
                        {
                            echo "<h6 class=\"ekklesia360_sermon_layout_meta\">";
                            if($show_meta_headings) echo "Speaker: ";
                            echo "{$item['preacher']}";
                            echo "</h6>";
                        }
                        if($show_passage && $item['passages'])
                        {
                            echo "<h6 class=\"ekklesia360_sermon_layout_meta\">";
                            if($show_meta_headings) echo "Passages: ";
                            echo "{$item['passages']}";
                            echo "</h6>";
                        }
                        if($show_media_links)
                        {
                            echo "<ul class=\"ekklesia360_sermon_layout_media\">";
                            if($item['videoplayer'])
                            {
                                $item['videoplayer'] = preg_replace('/<a(.+?)>.+?<\/a>/i',"<a$1><i class=\"fas fa-desktop\"></i></a>",$item['videoplayer']);
                                echo "<li class=\"ekklesia360_sermon_layout_media_videoplayer\">{$item['videoplayer']}</li>";
                            }
                            if($item['audioplayer'])
                            {
                                $item['audioplayer'] = preg_replace('/<a(.+?)>.+?<\/a>/i',"<a$1><i class=\"fas fa-volume-up\"></i></a>",$item['audioplayer']);
                                echo "<li class=\"ekklesia360_sermon_layout_media_audioplayer\">{$item['audioplayer']}</li>";
                            }
                            if($item['notes'])
                            {
                                echo "<li class=\"ekklesia360_sermon_layout_media_notes\"><a href=\"{$item['notes']}\" target=\"_blank\"><i class=\"fas fa-file-alt\"></i></a></li>";
                            }
                            echo "</ul>";
                        }
                        if($show_preview && $item['preview'])
                        {
                            $item['preview'] = substr($item['preview'], 0, 110)." ...";
                            echo "<p class=\"ekklesia360_sermon_layout_preview\">{$item['preview']}</p>";
                        }

                        if($detail_url && $detail_page_button_text)
                        {
                            echo "<p class=\"ekklesia360_sermon_layout_detail_button\"><a href=\"{$detail_url}?ekklesia360_sermon_slug={$item['slug']}\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">{$detail_page_button_text}</span></a></p>";
                        }

                        echo "</div>";
                        echo "</article>";
                    }
                    ?>
                </div>
                <?php
                if($show_pagination)
                {
                    $paginationOutput = '<p id="ekklesia360_sermon_layout_pagination" class="ekklesia360_pagination">'.$pagination->getLinks($_GET).'</p>';
                    $paginationOutput = str_replace('page=', 'ekklesia360_sermon_layout_page=', $paginationOutput);
                    //if complexity grows consider http_build_query

                    if(isset($_GET['search_term']))
                    {
                        $paginationOutput = str_replace('?', "?search_term={$_GET['search_term']}&", $paginationOutput);
                    }

                    //add group
                    if(isset($_GET['group']))
                    {
                        $paginationOutput = str_replace('?', "?group={$_GET['group']}&", $paginationOutput);
                    }
                    //add category
                    if(isset($_GET['category']))
                    {
                        $paginationOutput = str_replace('?', "?category={$_GET['category']}&", $paginationOutput);
                    }
                    //add series
                    if(isset($_GET['series']))
                    {
                        $paginationOutput = str_replace('?', "?series={$_GET['series']}&", $paginationOutput);
                    }
                    //add speaker
                    if(isset($_GET['speaker']))
                    {
                        $paginationOutput = str_replace('?', "?speaker={$_GET['speaker']}&", $paginationOutput);
                    }
                    echo $paginationOutput;
                }
            }
            //no output
            else
            {
                ?>

                <p>There are no sermons available.</p>

                <?php
            }
            ?>
        </div>
        <script>
            <?php if(count($_GET)): ?>
            const id = 'ekklesia360_sermon_layout_filters';
            const yOffset = -<?= $sticky_space ?>;
            const element = document.getElementById(id);
            const y = element.getBoundingClientRect().top + window.pageYOffset + yOffset;
            window.scrollTo({top: y, behavior: 'smooth'});
            <?php endif; ?>
            function filterEkklesia360Sermons(val) {
                document.getElementById('ekklesia360_sermon_layout_form').submit();
            }
        </script>
        <?php
    }
}