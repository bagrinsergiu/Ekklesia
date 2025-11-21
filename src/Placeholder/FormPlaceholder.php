<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class FormPlaceholder extends PlaceholderAbstract
{
    const NAME = 'ekk_form';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $atts = $placeholder->getAttributes();

        if (empty($atts['form'])) {
            return;
        }

        $isEditor = strpos($_SERVER['REQUEST_URI'], 'placeholders_bulks') || (isset($_POST['action']) && $_POST['action'] == 'brizy_placeholders_content');

        if ($isEditor) {
            $formUrl      = getenv('MB_FORM_URL') ?: 'https://forms.ministryforms.net';
            $twigFormHtml = file_get_contents(__DIR__ . '/../views/editor-form.html.twig');
            $template     = $this->twig->createTemplate($twigFormHtml);

            echo $template->render(
                [
                    'formId'   => $atts['form'],
                    'uniqueId' => md5($atts['form']),
                    'formUrl' => $formUrl
                ]
            );

            return;
        }

        $form = $this->monkCMS->get([
            'module'  => 'fmsform',
            'display' => 'detail',
            'find_id' => $atts['form'],
            'show'    => '__embedhtml__'
        ]);

        echo isset( $form['show']['embedhtml'] ) ? $form['show']['embedhtml'] : '';
    }
}
