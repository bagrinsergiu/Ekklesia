<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class SermonDetailPlaceholder extends PlaceholderAbstract
{
	protected $name = 'ekk_sermon_detail';

	public function echoValue( ContextInterface $context, ContentPlaceholder $placeholder )
	{
		$options = [
			'show_image'                => false,
			'show_video'                => false,
			'show_audio'                => false,
			'show_inline_video'         => false,
			'show_inline_audio'         => false,
			'show_media_links_video'    => false,
			'show_media_links_audio'    => false,
			'show_media_links_download' => false,
			'show_media_links_notes'    => false,
			'show_title'                => false,
			'show_date'                 => false,
			'show_category'             => false,
			'show_group'                => false,
			'show_series'               => false,
			'show_preacher'             => false,
			'show_passage'              => false,
			'show_meta_headings'        => false,
			'show_preview'              => false,
			'sermons_recent'            => '',
			'previous_page'             => false,
            'show_meta_icons'           => false
		];

		$settings = array_merge( $options, $placeholder->getAttributes() );
		$cms      = $this->monkCMS;

		extract( $settings );

		if ( ! empty( $_GET['mc-slug'] ) ) {
			$slug = $_GET['mc-slug'];
		} elseif ( $sermons_recent ) {
			$slug = $sermons_recent;
		} else {
			$recent = $cms->get( [
				'module'      => 'sermon',
				'display'     => 'list',
				'order'       => 'recent',
				'howmany'     => 1,
				'emailencode' => 'no',
				'show'        => "__audioplayer__",
			] );

			$slug = isset( $recent['show'][0]['slug'] ) ? $recent['show'][0]['slug'] : '';
		}

		$content = $cms->get( [
			'module'      => 'sermon',
			'display'     => 'detail',
			'find'        => $slug,
			'emailencode' => 'no',
			'show'        => "__audioplayer__",
		] );

        if (empty($content['show'])) {
            echo '<p>There is no sermon available.</p>';
            return;
        }

        $item = $content['show'];

        echo '<div class="brz-sermonDetail__container">';

            echo "<div class=\"brz-sermonDetail__item\">";

                if ( $show_title ) {
                    echo "<h2 class=\"brz-sermonDetail__item--meta--title brz-ministryBrands__item--meta-title\">{$item['title']}</h2>";
                }

                if ( $show_date && $item['date'] ) {
                    echo "<h6 class=\"brz-sermonDetail__item--meta brz-ministryBrands__item--meta-date\">";
                    if($show_meta_headings) {
                        if($show_meta_icons) echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path fill=\"currentColor\" d=\"M96 32V64H48C21.5 64 0 85.5 0 112v48H448V112c0-26.5-21.5-48-48-48H352V32c0-17.7-14.3-32-32-32s-32 14.3-32 32V64H160V32c0-17.7-14.3-32-32-32S96 14.3 96 32zM448 192H0V464c0 26.5 21.5 48 48 48H400c26.5 0 48-21.5 48-48V192z\"></path></svg>
</span>";
                        else echo "<span>Date: </span>";
                    }
                        echo "<span>{$item['date']}</span>";
                    echo "</h6>";
                }

                if ( $show_category && $item['category'] ) {
                    echo "<h6 class=\"brz-sermonDetail__item--meta brz-ministryBrands__item--meta-category\">";
                    if($show_meta_headings) {
                        if($show_meta_icons) echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 384 512\"><path fill=\"currentColor\" d=\"M0 48V487.7C0 501.1 10.9 512 24.3 512c5 0 9.9-1.5 14-4.4L192 400 345.7 507.6c4.1 2.9 9 4.4 14 4.4c13.4 0 24.3-10.9 24.3-24.3V48c0-26.5-21.5-48-48-48H48C21.5 0 0 21.5 0 48z\"></path></svg>
</span>";
                        else echo "<span>Category: </span>";
                    }
                        echo "<span>{$item['category']}</span>";
                    echo "</h6>";
                }

                if ( $show_group && $item['group'] ) {
                    echo "<h6 class=\"brz-sermonDetail__item--meta brz-ministryBrands__item--meta-group\">";
                        if($show_meta_headings) {
                            if($show_meta_icons) echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 640 512\"><path fill=\"currentColor\" d=\"M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z\"></path></svg>
</span>";
                            else echo "<span>Group: </span>";
                        }
                        echo "<span>{$item['group']}</span>";
                    echo "</h6>";
                }

                if ( $show_series && $item['series'] ) {
                    echo "<h6 class=\"brz-sermonDetail__item--meta brz-ministryBrands__item--meta-series\">";
                    if($show_meta_headings) {
                        if($show_meta_icons) echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\"><path fill=\"currentColor\" d=\"M40 48C26.7 48 16 58.7 16 72v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V72c0-13.3-10.7-24-24-24H40zM192 64c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zM16 232v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V232c0-13.3-10.7-24-24-24H40c-13.3 0-24 10.7-24 24zM40 368c-13.3 0-24 10.7-24 24v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V392c0-13.3-10.7-24-24-24H40z\"></path></svg>
</span>";
                        else echo "<span>Series: </span>";
                    }
                        echo "<span>{$item['series']}</span>";
                    echo "</h6>";
                }

                if ( $show_preacher && $item['preacher'] ) {
                    echo "<h6 class=\"brz-sermonDetail__item--meta brz-ministryBrands__item--meta-preacher\">";
                    if($show_meta_headings) {
                        if($show_meta_icons) echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path fill=\"currentColor\" d=\"M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z\"></path></svg>
</span>";
                        else echo "<span>Speaker: </span>";
                    }
                    echo "<span>{$item['preacher']}</span>";
                    echo "</h6>";
                }

                if ( $show_passage && $item['passages'] ) {
                    echo "<h6 class=\"brz-sermonDetail__item--meta--links brz-ministryBrands__item--meta-passage\">";
                    if($show_meta_headings) {
                        if($show_meta_icons) echo "<span class=\"brz-ministryBrands__meta--icons\"><svg class=\"brz-icon-svg align-[initial]\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 448 512\"><path fill=\"currentColor\" d=\"M96 0C43 0 0 43 0 96V416c0 53 43 96 96 96H384h32c17.7 0 32-14.3 32-32s-14.3-32-32-32V384c17.7 0 32-14.3 32-32V32c0-17.7-14.3-32-32-32H384 96zm0 384H352v64H96c-17.7 0-32-14.3-32-32s14.3-32 32-32zM208 80c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v48h48c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272V304c0 8.8-7.2 16-16 16H224c-8.8 0-16-7.2-16-16V192H160c-8.8 0-16-7.2-16-16V144c0-8.8 7.2-16 16-16h48V80z\"></path></svg>
</span>";
                        else echo "<span class='brz-sermonDetail__item--meta'>Passages: </span>";
                    }
                    echo "<span class=\"brz-ministryBrands__item--meta-passage-content\">{$item['passages']}</span>";
                    echo "</h6>";
                }

                if ( $show_image && $item['imageurl'] && ! $show_video ) {
                    echo "<div class=\"brz-ministryBrands__item--media\"><img src=\"{$item['imageurl']}\" alt=\"\" /></div>";
                }

                if ( $show_video ) {
                    if ( $item['videoembed'] ) {
                        echo "<div class=\"brz-ministryBrands__item--media\">{$item['videoembed']}</div>";
                    } elseif ( $item['videourl'] ) {
                        $videoext = pathinfo( $item['videourl'], PATHINFO_EXTENSION );
                        echo "<div class=\"brz-ministryBrands__item--media\">";
                            echo "<video src=\"{$item['videourl']}\" controls preload=\"none\" width=\"1024\" height=\"576\" poster=\"{$item['imageurl']}\" type=\"video/{$videoext}\"><p>The Video could not be played. Please <a href=\"{$item['videourl']}\" target=\"_blank\">download it here</a>.</p></video>";
                        echo "</div>";
                    } elseif ( $show_image && $item['imageurl'] ) {
                        echo "<div class=\"brz-ministryBrands__item--media\">
                                <img src=\"{$item['imageurl']}\" alt=\"\" />
                             </div>";
                    }
                }

                if ( $show_audio && $item['audiourl'] ) {
                    echo "<div class=\"brz-sermonDetail__item--media--audio\">";
                        echo "<audio src=\"{$item['audiourl']}\" controls preload=\"none\"></audio>";
                    echo "</div>";
                }

                if ( $show_media_links_video || $show_media_links_audio || $show_media_links_download || $show_media_links_notes ) {
                    echo "<ul class=\"brz-sermonDetail__item--media\">";

                        if ( $show_media_links_video && ! empty( $item['videoplayer'] ) ) {
                            if ( strpos( $item['videoplayer'], 'class' ) ) {
                                $item['videoplayer'] = str_replace( "Launch Player", "Watch", $item['videoplayer'] );
                                $item['videoplayer'] = str_replace( "Watch Video", "Watch", $item['videoplayer'] );
                                $item['videoplayer'] = str_replace( 'mcms_videoplayer', 'mcms_videoplayer brz-button-link brz-button brz-size-sm ', $item['videoplayer'] );
                            } else {
                                $item['videoplayer'] = str_replace( ">Launch Player", " class=\"brz-button-link brz-button brz-size-sm\">Watch", $item['videoplayer'] );
                                $item['videoplayer'] = str_replace( ">Watch Video", " class=\"brz-button-link brz-button brz-size-sm\">Watch", $item['videoplayer'] );
                            }

                            echo "<li class=\"brz-ministryBrands__item--meta--links\">{$item['videoplayer']}</li>";
                        }

                        if ( $show_media_links_audio && ! empty( $item['audioplayer'] ) ) {
                            $item['audioplayer'] = str_replace( "Launch Player", "Listen", $item['audioplayer'] );
                            $item['audioplayer'] = str_replace( 'mcms_audioplayer', 'mcms_audioplayer brz-button-link brz-button brz-size-sm ', $item['audioplayer'] );
                            echo "<li class=\"brz-ministryBrands__item--meta--links\">{$item['audioplayer']}</li>";
                        }

                        if ( $show_media_links_download && ! empty( $item['audio'] ) ) {
                            echo "<li class=\"brz-ministryBrands__item--meta--links\"><a href=\"{$item['audio']}\" class=\"brz-button-link brz-button brz-size-sm\">Download Audio</a></li>";
                        }

                        if ( $show_media_links_notes && ! empty( $item['notes'] ) ) {
                            echo "<li class=\"brz-ministryBrands__item--meta--links\"><a href=\"{$item['notes']}\" class=\"brz-button-link brz-button brz-size-sm\" target=\"_blank\">Notes</a></li>";
                        }
                    echo "</ul>";
                }

                if ( $show_preview && ! empty( $item['text'] ) ) {
                    echo "<div class=\"brz-sermonDetail__item--meta--preview\">{$item['text']}</div>";
                }

                if ( $previous_page ) {
                    echo '<div class="brz-ministryBrands__item--meta--links brz-ministryBrands__item--meta--links--previous">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512" class="brz-icon-svg align-[initial]"><path d="M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z"></path></svg>
                    Previous Page</div>';
                }
            echo "</div>";
        echo '</div>';
	}
}
