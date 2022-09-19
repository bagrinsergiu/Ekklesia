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

class Placeholders
{
    /**
     * @var MonkCms
     */
    private $monkCms;

    public function __construct(MonkCms $monkCms)
    {
        $this->monkCms = $monkCms;
    }

    public function getPlaceholders()
    {
        return [
            new SermonListPlaceholder($this->monkCms),
            new SermonLayoutPlaceholder($this->monkCms),
            new SermonDetailPlaceholder($this->monkCms),
            new SermonFeaturedPlaceholder($this->monkCms),
            new EventListPlaceholder($this->monkCms),
            new EventCalendarPlaceholder($this->monkCms),
            new EventLayoutPlaceholder($this->monkCms),
            new EventDetailPlaceholder($this->monkCms),
            new EventFeaturedPlaceholder($this->monkCms),
            new GroupListPlaceholder($this->monkCms),
            new GroupSliderPlaceholder($this->monkCms),
            new GroupLayoutPlaceholder($this->monkCms),
            new GroupDetailPlaceholder($this->monkCms),
            new GroupFeaturedPlaceholder($this->monkCms),
        ];
    }
}