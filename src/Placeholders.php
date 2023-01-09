<?php

namespace BrizyEkklesia;

use BrizyEkklesia\Placeholder\EventCalendarPlaceholder;
use BrizyEkklesia\Placeholder\EventDetailPlaceholder;
use BrizyEkklesia\Placeholder\EventFeaturedPlaceholder;
use BrizyEkklesia\Placeholder\EventLayoutPlaceholder;
use BrizyEkklesia\Placeholder\EventListPlaceholder;
use BrizyEkklesia\Placeholder\FormPlaceholder;
use BrizyEkklesia\Placeholder\GroupDetailPlaceholder;
use BrizyEkklesia\Placeholder\GroupFeaturedPlaceholder;
use BrizyEkklesia\Placeholder\GroupLayoutPlaceholder;
use BrizyEkklesia\Placeholder\GroupListPlaceholder;
use BrizyEkklesia\Placeholder\GroupSliderPlaceholder;
use BrizyEkklesia\Placeholder\SermonDetailPlaceholder;
use BrizyEkklesia\Placeholder\SermonFeaturedPlaceholder;
use BrizyEkklesia\Placeholder\SermonLayoutPlaceholder;
use BrizyEkklesia\Placeholder\SermonListPlaceholder;
use Twig_Environment;

class Placeholders
{
    /**
     * @var MonkCms
     */
    private $monkCms;

    /**
     * @var Twig_Environment
     */
    private $twig;

    public function __construct(MonkCms $monkCms, Twig_Environment $twig)
    {
        $this->monkCms = $monkCms;
        $this->twig    = $twig;
    }

    public function getPlaceholders()
    {
        return [
            new SermonListPlaceholder($this->monkCms, $this->twig),
            new SermonLayoutPlaceholder($this->monkCms, $this->twig),
            new SermonDetailPlaceholder($this->monkCms, $this->twig),
            new SermonFeaturedPlaceholder($this->monkCms, $this->twig),
            new EventListPlaceholder($this->monkCms, $this->twig),
            new EventCalendarPlaceholder($this->monkCms, $this->twig),
            new EventLayoutPlaceholder($this->monkCms, $this->twig),
            new EventDetailPlaceholder($this->monkCms, $this->twig),
            new EventFeaturedPlaceholder($this->monkCms, $this->twig),
            new GroupListPlaceholder($this->monkCms, $this->twig),
            new GroupSliderPlaceholder($this->monkCms, $this->twig),
            new GroupLayoutPlaceholder($this->monkCms, $this->twig),
            new GroupDetailPlaceholder($this->monkCms, $this->twig),
            new GroupFeaturedPlaceholder($this->monkCms, $this->twig),
            new FormPlaceholder($this->monkCms, $this->twig)
        ];
    }
}