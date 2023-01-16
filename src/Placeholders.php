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
use BrizyPlaceholders\Replacer;
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

    public function getPlaceholders(Replacer $replacer)
    {
        return [
            new SermonListPlaceholder($this->monkCms, $this->twig, $replacer),
            new SermonLayoutPlaceholder($this->monkCms, $this->twig, $replacer),
            new SermonDetailPlaceholder($this->monkCms, $this->twig, $replacer),
            new SermonFeaturedPlaceholder($this->monkCms, $this->twig, $replacer),
            new EventListPlaceholder($this->monkCms, $this->twig, $replacer),
            new EventCalendarPlaceholder($this->monkCms, $this->twig, $replacer),
            new EventLayoutPlaceholder($this->monkCms, $this->twig, $replacer),
            new EventDetailPlaceholder($this->monkCms, $this->twig, $replacer),
            new EventFeaturedPlaceholder($this->monkCms, $this->twig, $replacer),
            new GroupListPlaceholder($this->monkCms, $this->twig, $replacer),
            new GroupSliderPlaceholder($this->monkCms, $this->twig, $replacer),
            new GroupLayoutPlaceholder($this->monkCms, $this->twig, $replacer),
            new GroupDetailPlaceholder($this->monkCms, $this->twig, $replacer),
            new GroupFeaturedPlaceholder($this->monkCms, $this->twig, $replacer),
            new FormPlaceholder($this->monkCms, $this->twig, $replacer)
        ];
    }
}