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
        $detail_url      = $settings['detail_page'] ? $this->replacer->replacePlaceholders(urldecode($settings['detail_page']), $context) : false;
        $cms             = $this->monkCMS;
        $page            = isset($_GET['ekk-sermon-layout-page']) ? $_GET['ekk-sermon-layout-page'] : 1;
        $category_filter = isset($_GET['ekk-category']) ? $_GET['ekk-category'] : false;
        $group_filter    = isset($_GET['ekk-group']) ? $_GET['ekk-group'] : false;
        $series_filter   = isset($_GET['ekk-series']) ? $_GET['ekk-series'] : false;
        $speaker_filter  = isset($_GET['ekk-speaker']) ? $_GET['ekk-speaker'] : false;

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
        if (isset($_GET['ekk-search_term'])) {
            $content = array();
            $search_arr = $cms->get(array(
                'module'  => 'search',
                'display' => 'results',
                'howmany' => '100',
                'keywords' => $_GET['ekk-search_term'],
                'find_module' => 'sermon',
                'hide_module' => 'media',
            ));
            if(isset($search_arr['show'])){
                foreach($search_arr['show'] as $search){
                    $item = $cms->get(array(
                        'module'  => 'sermon',
                    'display' => 'detail',
                    'emailencode' => 'no',
                    'find' => $search['slug'],
                    'show' => "__audioplayer__",
                ));
                $content['show'][] = $item['show'];
            }
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
                'show' => "__audioplayer__",
            ));
        }

        ?>

        <div class="brz-sermonLayout__filter">
            <form id="brz-sermonLayout__filter--form" name="brz-sermonLayout__filter--form" class="brz-sermonLayout__filter--form" action="<?= $baseURL ?>" data-count="<?= $filterCount ?>">

                <?php if($show_group_filter && count($groups['group_show']) > 0): ?>
                    <div class="brz-sermonLayout__filter--form-selectWrapper">
                    <select name="ekk-group" class='sorter' >
                        <option value=""><?= $group_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        foreach($groups['group_show'] as $group)
                        {
                            echo "<option value=\"{$group['slug']}\"";
                            if(isset($_GET['ekk-group']) && $_GET['ekk-group'] == $group['slug'])
                            {
                                echo " selected";
                            }
                            echo ">{$group['title']}</option>";
                        }
                        ?>
                    </select>
                    </div>
                <?php endif; ?>

                <?php if($show_category_filter && count($categories['group_show']) > 0): ?>
                    <div class="brz-sermonLayout__filter--form-selectWrapper">
                    <select name="ekk-category" class='sorter' >
                        <option value=""><?= $category_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        foreach($categories['group_show'] as $category)
                        {
                            echo "<option value=\"{$category['slug']}\"";
                            if(isset($_GET['ekk-category']) && $_GET['ekk-category'] == $category['slug'])
                            {
                                echo " selected";
                            }
                            echo ">{$category['title']}</option>";
                        }
                        ?>
                    </select>
                    </div>
                <?php endif; ?>

                <?php if($show_series_filter && count($series['group_show']) > 0): ?>
                    <div class="brz-sermonLayout__filter--form-selectWrapper">
                    <select name="ekk-series" class='sorter' >
                        <option value=""><?= $series_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        foreach($series['group_show'] as $serie)
                        {
                            echo "<option value=\"{$serie['slug']}\"";
                            if(isset($_GET['ekk-series']) && $_GET['ekk-series'] == $serie['slug'])
                            {
                                echo " selected";
                            }
                            echo ">{$serie['title']}</option>";
                        }
                        ?>
                    </select>
                    </div>
                <?php endif; ?>

                <?php if($show_speaker_filter && count($speakers['group_show']) > 0): ?>
                    <div class="brz-sermonLayout__filter--form-selectWrapper">
                    <select name="ekk-speaker" class='sorter' >
                        <option value=""><?= $speaker_filter_heading ?></option>
                        <option value="">All</option>
                        <?php
                        foreach($speakers['group_show'] as $speaker)
                        {
                            echo "<option value=\"{$speaker['slug']}\"";
                            if(isset($_GET['ekk-speaker']) && $_GET['ekk-speaker'] == $speaker['slug'])
                            {
                                echo " selected";
                            }
                            echo ">{$speaker['title']}</option>";
                        }
                        ?>
                    </select>
                    </div>
                <?php endif; ?>

            </form>

            <?php if($show_search): ?>
                <form method="get" id="brz-sermonLayout__filter--form-search" name="brz-sermonLayout__filter--form-search" class="brz-sermonLayout__filter--form-search" action="<?= $baseURL ?>" data-count="<?= $filterCount ?>">
                    <fieldset>
                        <input type="text" id="ekklesia360_sermon_layout_search_term" name="search_term" value="" placeholder="<?= $search_placeholder ?>" />
                        <button type="submit" name="submit" id="ekklesia360_sermon_layout_search_submit" value=""><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="brz-icon-svg" data-type="fa" data-name="search"><path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path></svg></button>
                    </fieldset>
                </form>
            <?php endif; ?>
        </div>

        <?php if(isset($_GET['ekk-search_term']))
    {
        echo "<h4 class=\"ekklesia360_sermon_layout_results_heading\"><a href=\"{$baseURL}\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 352 512\" class=\"brz-icon-svg\" data-type=\"fa\" data-name=\"times\"><path d=\"M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z\"></path></svg></a> Search results for \"{$_GET['ekk-search_term']}\"</h4>";
    }
        ?>

        <div class="brz-sermonLayout__container">

            <?php

            //setup pagination
            $_content =  isset($content["show"]) ? $content["show"]  : [];
            $pagination = new CustomPagination($_content, (isset($page) ? $page : 1), $howmany);
            $pagination->setShowFirstAndLast(true);
            $resultsPagination = $pagination->getResults();
            //output

            //output
            if(isset($resultsPagination) && count($resultsPagination) > 0)
            {
                ?>
                    <?php
                    foreach($resultsPagination as $key => $item)
                    {
                        echo "<div class=\"brz-sermonLayout__item\">";

                        if( $show_images && $item['imageurl'] && !$show_video)
                        {
                            if($detail_url) echo "<a href=\"{$detail_url}?ekk-sermon-slug={$item['slug']}\">";
                            echo "<div class=\"brz-sermonLayout__item--media--image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                            if($detail_url) echo "</a>";
                        }
                        if( $show_video )
                        {
                            if($item['videoembed'])
                            {
                                echo "<div class=\"brz-sermonLayout__item--media--video\">{$item['videoembed']}</div>";
                            }
                            elseif($item['videourl'])
                            {
                                $videoext = pathinfo($item['videourl'], PATHINFO_EXTENSION);
                                echo "<div class=\"brz-sermonLayout__item--media--videoResponsive\">";
                                echo "<video src=\"{$item['videourl']}\" controls preload=\"none\" width=\"1024\" height=\"576\" poster=\"{$item['imageurl']}\" type=\"video/{$videoext}\"><p>The Video could not be played. Please <a href=\"{$item['videourl']}\" target=\"_blank\">download it here</a>.</p></video>";
                                echo "</div>";
                            }
                            elseif($show_image && $item['imageurl'])
                            {
                                echo "<div class=\"brz-sermonLayout__item--media--image\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                            }
                        }
                        if($show_audio && $item['audiourl'] )
                        {
                            echo "<div class=\"brz-sermonLayout__item--media--audio\">";
                            echo "<audio src=\"{$item['audiourl']}\" controls preload=\"none\"></audio>";
                            echo "</div>";
                        }

                        if($show_title )
                        {
                            echo "<h4 class=\"brz-sermonLayout__item--meta--title\">";
                            if($detail_url) echo "<a href=\"{$detail_url}?ekk-sermon-slug={$item['slug']}\">";
                            echo "{$item['title']}";
                            if($detail_url) echo "</a>";
                            echo "</h4>";
                        }

                        if($show_date && $item['date'])
                        {
                            echo "<h6 class=\"brz-sermonLayout__item--meta\">";
                            if($show_meta_headings) echo "Date: ";
                            echo "{$item['date']}";
                            echo "</h6>";
                        }
                        if($show_category && $item['category'])
                        {
                            echo "<h6 class=\"brz-sermonLayout__item--meta\">";
                            if($show_meta_headings) echo "Category: ";
                            echo "{$item['category']}";
                            echo "</h6>";
                        }
                        if($show_group && $item['group'])
                        {
                            echo "<h6 class=\"brz-sermonLayout__item--meta\">";
                            if($show_meta_headings) echo "Group: ";
                            echo "{$item['group']}";
                            echo "</h6>";
                        }
                        if($show_series && $item['series'])
                        {
                            echo "<h6 class=\"brz-sermonLayout__item--meta\">";
                            if($show_meta_headings) echo "Series: ";
                            echo "{$item['series']}";
                            echo "</h6>";
                        }
                        if($show_preacher && $item['preacher'])
                        {
                            echo "<h6 class=\"brz-sermonLayout__item--meta\">";
                            if($show_meta_headings) echo "Speaker: ";
                            echo "{$item['preacher']}";
                            echo "</h6>";
                        }
                        if($show_passage && $item['passages'])
                        {
                            echo "<h6>";
                            if($show_meta_headings) echo "<span class='brz-sermonLayout__item--meta'>Passages: </span>";
                            echo "{$item['passages']}";
                            echo "</h6>";
                        }
                        if($show_media_links)
                        {
                            echo "<ul class=\"brz-sermonLayout__item--media\">";
                            if($item['videoplayer'])
                            {
                                $item['videoplayer'] = preg_replace('/<a(.+?)>.+?<\/a>/i',"<a$1><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 576 512\" class=\"brz-icon-svg\" data-type=\"fa\" data-name=\"desktop\"><path d=\"M528 0H48C21.5 0 0 21.5 0 48v320c0 26.5 21.5 48 48 48h192l-16 48h-72c-13.3 0-24 10.7-24 24s10.7 24 24 24h272c13.3 0 24-10.7 24-24s-10.7-24-24-24h-72l-16-48h192c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48zm-16 352H64V64h448v288z\"></path></svg></a>",$item['videoplayer']);                                
                                echo "<li class=\"brz-sermonLayout__item--media_videoplayer\">{$item['videoplayer']}</li>";
                            }
                            if($item['audioplayer'])
                            {
                                $item['audioplayer'] = preg_replace('/<a(.+?)>.+?<\/a>/i',"<a$1><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 576 512\" class=\"brz-icon-svg\" data-type=\"fa\" data-name=\"volume-up\"><path d=\"M215.03 71.05L126.06 160H24c-13.26 0-24 10.74-24 24v144c0 13.25 10.74 24 24 24h102.06l88.97 88.95c15.03 15.03 40.97 4.47 40.97-16.97V88.02c0-21.46-25.96-31.98-40.97-16.97zm233.32-51.08c-11.17-7.33-26.18-4.24-33.51 6.95-7.34 11.17-4.22 26.18 6.95 33.51 66.27 43.49 105.82 116.6 105.82 195.58 0 78.98-39.55 152.09-105.82 195.58-11.17 7.32-14.29 22.34-6.95 33.5 7.04 10.71 21.93 14.56 33.51 6.95C528.27 439.58 576 351.33 576 256S528.27 72.43 448.35 19.97zM480 256c0-63.53-32.06-121.94-85.77-156.24-11.19-7.14-26.03-3.82-33.12 7.46s-3.78 26.21 7.41 33.36C408.27 165.97 432 209.11 432 256s-23.73 90.03-63.48 115.42c-11.19 7.14-14.5 22.07-7.41 33.36 6.51 10.36 21.12 15.14 33.12 7.46C447.94 377.94 480 319.54 480 256zm-141.77-76.87c-11.58-6.33-26.19-2.16-32.61 9.45-6.39 11.61-2.16 26.2 9.45 32.61C327.98 228.28 336 241.63 336 256c0 14.38-8.02 27.72-20.92 34.81-11.61 6.41-15.84 21-9.45 32.61 6.43 11.66 21.05 15.8 32.61 9.45 28.23-15.55 45.77-45 45.77-76.88s-17.54-61.32-45.78-76.86z\"></path></svg></a>",$item['audioplayer']);
                                echo "<li class=\"brz-sermonLayout__item--media_audioplayer\">{$item['audioplayer']}</li>";
                            }
                            if($item['notes'])
                            {
                                echo "<li class=\"brz-sermonLayout__item--media_notes\"><a href=\"{$item['notes']}\" target=\"_blank\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 384 512\" class=\"brz-icon-svg\" data-type=\"fa\" data-name=\"file-alt\"><path d=\"M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm64 236c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-64c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12v8zm0-72v8c0 6.6-5.4 12-12 12H108c-6.6 0-12-5.4-12-12v-8c0-6.6 5.4-12 12-12h168c6.6 0 12 5.4 12 12zm96-114.1v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z\"></path></svg></a></li>";
                            }
                            echo "</ul>";
                        }
                        if($show_preview && $item['preview'])
                        {
                            $item['preview'] = substr($item['preview'], 0, 110)." ...";
                            echo "<p class=\"brz-sermonLayout__item--preview\">{$item['preview']}</p>";
                        }

                        if($detail_url && $detail_page_button_text)
                        {
                            // need to look later if this url is created correctly
                            echo "<p class=\"brz-sermonLayout__item--detail-button\"><a href=\"{$detail_url}\" class=\"brz-button-link brz-button brz-size-sm\"><span class=\"brz-button-text\">{$detail_page_button_text}</span></a></p>";
                        }

                        echo "</div>";
                    }
                    ?>
                <?php
                
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
        <?php 
            if($show_pagination)
            {
                $paginationOutput = '<p class="brz-ministryBrands__pagination">' . $pagination->getLinks($_GET, 'ekk-sermon-layout-page') . '</p>';

                //if complexity grows consider http_build_query
                if(isset($_GET['ekk-search_term']))
                {
                    $paginationOutput = str_replace('?', "?ekk-search_term={$_GET['ekk-search_term']}&", $paginationOutput);
                }

                //add group
                if(isset($_GET['ekk-group']))
                {
                    $paginationOutput = str_replace('?', "?ekk-group={$_GET['ekk-group']}&", $paginationOutput);
                }
                //add category
                if(isset($_GET['ekk-category']))
                {
                    $paginationOutput = str_replace('?', "?ekk-category={$_GET['ekk-category']}&", $paginationOutput);
                }
                //add series
                if(isset($_GET['ekk-series']))
                {
                    $paginationOutput = str_replace('?', "?ekk-series={$_GET['ekk-series']}&", $paginationOutput);
                }
                //add speaker
                if(isset($_GET['ekk-speaker']))
                {
                    $paginationOutput = str_replace('?', "?ekk-speaker={$_GET['ekk-speaker']}&", $paginationOutput);
                }
                echo $paginationOutput;
            }
        ?>
        <?php
    }
}