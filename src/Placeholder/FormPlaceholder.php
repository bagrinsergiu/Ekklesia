<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class FormPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_form';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $atts = $placeholder->getAttributes();

        if (empty($atts['form'])) {
            return;
        }

        $form = $this->monkCMS->get([
            'module'  => 'fmsform',
            'display' => 'detail',
            'find_id' => $atts['form'],
            'show'    => '__embedhtml__'
        ]);

        echo $form['show']['embedhtml'] ?? '';
    }
}
