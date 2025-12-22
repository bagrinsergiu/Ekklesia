<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class PrayerPlaceholder extends PlaceholderAbstract
{
    const NAME = 'ekk_prayer';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $form = $this->monkCMS->get([
            'module'  => 'fmsform',
            'display' => 'detail',
            'find'    => 'prayer-request',
            'show'    => '__embedhtml__'
        ]);

        $formId = $form['show']['id'];
        $isEditor = strpos($_SERVER['REQUEST_URI'], 'placeholders_bulks') || (isset($_POST['action']) && $_POST['action'] == 'brizy_placeholders_content');

        if ($isEditor) {
            $uniqueId = md5(json_encode($form));
            $formUrl  = getenv('MB_FORM_URL') ?: 'https://forms.ministryforms.net';
            echo "<iframe id=\"mb-formbuilder-container\" data-uniqueid=\"{$uniqueId}\" src=\"{$formUrl}/viewForm.aspx?formid={$formId}&direct-link=&embed=true&frameid={$uniqueId}\" style=\"width: 100%; height: 95%; border:0\" allow=\"payment\"></iframe>";
            return;
        }
        echo isset($form['show']['embedhtml']) ? $form['show']['embedhtml'] : '';
    }
}
