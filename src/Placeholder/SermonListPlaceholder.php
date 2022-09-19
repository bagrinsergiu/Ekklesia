<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class SermonListPlaceholder extends PlaceholderAbstract
{
    protected $name = 'ekk_sermon_list';

    public function getValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        $attributes = $placeholder->getAttributes();

        $features = isset($attributes['features']) ? 'features' : '';
        $nonfeatures = isset($attributes['nonfeatures']) ? 'nonfeatures' : '';

        if ($features) {
            $nonfeatures = '';
        } elseif ($nonfeatures) {
            $features = '';
        }

        if (!$content = $this->monkCMS->get([
            'module' => 'sermon',
            'display' => 'list',
            'order' => 'recent',
            'emailencode' => 'no',
            'howmany' => isset($attributes['howmany']) ? intval(round($attributes ['howmany'])) : 9,
            'page' => $attributes['page'] ?? 1,
            'find_category' => (isset($attributes ['category']) && $attributes ['category'] != 'all') ? $attributes ['category'] : '',
            'find_group' => (isset($attributes ['group']) && $attributes ['group'] != 'all') ? $attributes ['group'] : '',
            'find_series' => (isset($attributes ['series']) && $attributes ['series'] != 'all') ? $attributes ['series'] : '',
            'features' => $features,
            'nonfeatures' => $nonfeatures,
            'show' => "__videoplayer fullscreen='true'__",
            'show' => "__audioplayer__",
            'after_show' => '__pagination__'
        ])) {
            return null;
        }

        $twigHtml = file_get_contents(
            __DIR__ . '/../views/sermon_list.html.twig'
        );

        $template = $this->twig->createTemplate($twigHtml);

        return htmlspecialchars_decode($template->render([
                'content' => $content,
                'column_count' => $attributes['column_count'] ?? '3',
                'column_count_tablet' => $attributes['column_count_tablet'] ?? '2',
                'column_count_mobile' => $attributes['column_count_mobile'] ?? '1',
                'show_pagination' => isset($attributes['show_pagination']),
                'feautres' => $features,
                'nonfeatures' => $nonfeatures,
                'show_images' => isset($attributes ['show_images']),
                'show_video' => isset($attributes ['show_inline_video']),
                'show_audio' => isset($attributes ['show_inline_audio']),
                'show_media_links' => isset($attributes ['show_media_links']),
                'show_title' => isset($attributes ['show_title']),
                'show_date' => isset($attributes['show_date']),
                'show_category' => isset($attributes ['show_category']),
                'show_group' => isset($attributes ['show_group']),
                'show_series' => isset($attributes ['show_series']),
                'show_preacher' => isset($attributes ['show_preacher']),
                'show_passage' => isset($attributes ['show_passage']),
                'show_meta_headings' => isset($attributes ['show_meta_headings']),
                'show_preview' => isset($attributes ['show_preview']),
                'detail_page_button_text' => $attributes ['detail_page_button_text'] ?? false,
                'detail_url' => $attributes ['detail_page'] ?? false,
                'sticky_space' => isset($attributes ['sticky_space']) ? (int)$attributes ['sticky_space'] : '0'
            ]
        ));
    }
}