<?php

namespace BrizyEkklesia;

use BrizyEkklesia\Placeholder\Ekklesia360\EventCalendarPlaceholder;
use BrizyEkklesia\Placeholder\Ekklesia360\EventDetailPlaceholder;
use BrizyEkklesia\Placeholder\Ekklesia360\EventFeaturedPlaceholder;
use BrizyEkklesia\Placeholder\Ekklesia360\EventLayoutPlaceholder;
use BrizyEkklesia\Placeholder\Ekklesia360\EventListPlaceholder;
use BrizyEkklesia\Placeholder\Ekklesia360\GroupDetailPlaceholder;
use BrizyEkklesia\Placeholder\Ekklesia360\GroupFeaturedPlaceholder;
use BrizyEkklesia\Placeholder\Ekklesia360\GroupLayoutPlaceholder;
use BrizyEkklesia\Placeholder\Ekklesia360\GroupListPlaceholder;
use BrizyEkklesia\Placeholder\Ekklesia360\GroupSliderPlaceholder;
use BrizyEkklesia\Placeholder\Ekklesia360\SermonDetailPlaceholder;
use BrizyEkklesia\Placeholder\Ekklesia360\SermonFeaturedPlaceholder;
use BrizyEkklesia\Placeholder\Ekklesia360\SermonLayoutPlaceholder;
use BrizyEkklesia\Placeholder\Ekklesia360\SermonListPlaceholder;
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
        ];
    }
}