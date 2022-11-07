<?php

namespace BrizyEkklesia;

use Exception;

class Api {

    /**
     * @var MonkCms
     */
    private $monkCms;

    public function __construct(MonkCms $monkCms)
    {
        $this->monkCms = $monkCms;
    }

    public function getCats($module)
    {
        $cats = $this->monkCms->get([
            'module'  => $module,
            'display' => 'categories'
        ]);

        $options = ["all"=>"All"];

        foreach ($cats['show'] as $category) {
            $options[$category['slug']] = $category['name'];
        }

        return $options;
    }

    public function getGroups()
    {
        $groups = $this->monkCms->get([
            'module'  => 'group',
            'display' => 'list'
        ]);

        $options = ["all"=>"All"];
        foreach ($groups['show'] as $group) {
            $options[$group['slug']] = $group['title'];
        }

        return $options;
    }

    public function getSeries()
    {
        $series = $this->monkCms->get([
            'module'  => 'sermon',
            'display' => 'list',
            'groupby' => 'series'
        ]);

        $options = ["all"=>"All"];

        foreach ($series['group_show'] as $serie) {
            $options[$serie['slug']] = $serie['title'];
        }

        return $options;
    }

    /**
     * @param string $module - sermon|event
     *
     * @return array
     * @throws Exception
     */
    public function getRecent($module)
    {
        $recents = $this->monkCms->get([
            'module'      => $module,
            'display'     => 'list',
            'order'       => 'recent',
            'howmany'     => 20,
            'emailencode' => 'no',
        ]);

        $options = ["all"=>"All"];

        foreach ($recents['show'] as $recent) {
            $options[$recent['slug']] = isset($recent['title']) ? $recent['title'] : $recent['name'];
        }

        return $options;
    }

    public function getCatsLevels($module)
    {
        $cats = $this->monkCms->get([
            'module'  => $module,
            'display' => 'categories'
        ]);

        $parents = [];
        foreach ($cats['level1'] as $cat) {
            $parents[$cat['slug']] = $cat['name'];
        }
        asort($parents);

        $childs = [];
        foreach ($cats['level2'] as $cat) {
            $childs[$cat['bid']] = $cat['name'];
        }
        asort($childs);

        return [
            'parents' => $parents,
            'childs'  => $childs,
        ];
    }
}