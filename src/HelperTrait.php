<?php

namespace BrizyEkklesia;

trait HelperTrait
{
    public function excerpt(string $content, string $more = ' &hellip;', int $nrWords = 15): string
    {
        $content = strip_tags($content);
        $words   = preg_split("/[\n\r\t ]+/", $content, $nrWords + 1, PREG_SPLIT_NO_EMPTY);

        if (count($words) > $nrWords) {
            array_pop($words);
            $content = implode(' ', $words) . $more;
        } else {
            $content = implode(' ', $words);
        }

        return $content;
    }
}