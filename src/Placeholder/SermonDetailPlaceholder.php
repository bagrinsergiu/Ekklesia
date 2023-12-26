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
			'previous_page'             => false
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
                    echo "<h2 class=\"brz-sermonDetail__item--meta--title\">{$item['title']}</h2>";
                }

                if ( $show_date && $item['date'] ) {
                    echo "<h6 class=\"brz-sermonDetail__item--meta\">";
                        if ( $show_meta_headings ) {
                            echo "Date: ";
                        }
                        echo "{$item['date']}";
                    echo "</h6>";
                }

                if ( $show_category && $item['category'] ) {
                    echo "<h6 class=\"brz-sermonDetail__item--meta\">";
                        if ( $show_meta_headings ) {
                            echo "Category: ";
                        }
                        echo "{$item['category']}";
                    echo "</h6>";
                }

                if ( $show_group && $item['group'] ) {
                    echo "<h6 class=\"brz-sermonDetail__item--meta\">";
                        if ( $show_meta_headings ) {
                            echo "Group: ";
                        }
                        echo "{$item['group']}";
                    echo "</h6>";
                }

                if ( $show_series && $item['series'] ) {
                    echo "<h6 class=\"brz-sermonDetail__item--meta\">";
                        if ( $show_meta_headings ) {
                            echo "Series: ";
                        }
                        echo "{$item['series']}";
                    echo "</h6>";
                }

                if ( $show_preacher && $item['preacher'] ) {
                    echo "<h6 class=\"brz-sermonDetail__item--meta\">";
                        if ( $show_meta_headings ) {
                            echo "Speaker: ";
                        }
                        echo "{$item['preacher']}";
                    echo "</h6>";
                }

                if ( $show_passage && $item['passages'] ) {
                    echo "<h6 class=\"brz-sermonDetail__item--meta--links\">";
                        if ( $show_meta_headings ) {
                            echo "<span class='brz-sermonDetail__item--meta'>Passages: </span>";
                        }
                        echo "{$item['passages']}";
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
