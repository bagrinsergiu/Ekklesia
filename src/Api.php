<?php

namespace BrizyEkklesia;

class Api {
	/**
	 * @var MonkCms
	 */
	private $monkCms;

	public function __construct(MonkCms $monkCms)
	{
		$this->monkCms = $monkCms;
	}

	public function getSermonCategories() {

		$categories = $this->monkCms->get(array(
			'module'  => 'sermon',
			'display' => 'categories'
		));

		$options = [];

		foreach($categories['show'] as $category) {
			$options[$category['slug']] = $category['name'];
		}

		return $options;
	}

	public function getEventCategories() {

		$categories = $this->monkCms->get(array(
			'module'  => 'sermon',
			'display' => 'categories'
		));

		$options = [];

		foreach($categories['show'] as $category) {
			$options[$category['slug']] = $category['name'];
		}

		return $options;
	}

	public function getGroups() {
		$groups = $this->monkCms->get(array(
			'module'  => 'group',
			'display' => 'list'
		));

		$options = [];
		foreach($groups['show'] as $group) {
			$options[$group['slug']] = $group['title'];
		}

		return $options;
	}

	public function getSeries() {
		$series = $this->monkCms->get(array(
			'module'  => 'sermon',
			'display' => 'list',
			'groupby' => 'series'
		));

		$options = [];

		foreach($series['group_show'] as $serie) {
			$options[$serie['slug']] = $serie['title'];
		}

		return $options;
	}

	public function getRecentSermons() {
		$recentSermons = $this->monkCms->get([
			'module'      => 'sermon',
			'display'     => 'list',
			'order'       => 'recent',
			'howmany'     => 20,
			'emailencode' => 'no',
		]);

		$options = [];

		foreach($recentSermons['show'] as $recent) {
			$options[$recent['slug']] = $recent['title'];
		}

		return $options;
	}


}