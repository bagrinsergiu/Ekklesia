<?php

namespace BrizyEkklesia;

trait HelperTrait
{
    public function excerpt(string $content, string $more = ' &hellip;', int $length = 110): string
    {
        $content = strip_tags($content);

        if (strlen($content) > $length) {
            $content = substr($content, 0, $length) . $more;
        }

        return $content;
    }
}