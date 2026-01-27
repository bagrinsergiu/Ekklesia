<?php

namespace BrizyEkklesia\Placeholder;

use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;

class PrayerButtonPlaceholder extends PlaceholderAbstract
{
    const NAME = 'ekk_prayer_button';

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        echo '<div id="prayer-button-modal" class="brz-ministryBrands-PrayerButton-modal">
        <div id="emb_prayerform" class="brz-ministryBrands-PrayerButton-form">
            <form id="EmbPrayerForm" tabindex="-1" role="dialog"
                  method="post">
              
                <input type="hidden" name="action" value="ajaxEmbCreatePrayer"/>
                <div class="brz-ministryBrands-PrayerButton-modal-dialog brz-ministryBrands-PrayerButton-modal-dialog-centered" role="document">
                    <div class="brz-ministryBrands-PrayerButton-modal-content brz-p-relative">
                        <div class="brz-p-absolute" style="top: 0; left: 0; right: 0; bottom: 0; display: none; justify-content: center; align-items: center; background: rgba(255,255,255,0.7); z-index: 1051;">
                            <div class="brz-ministryBrands-PrayerButton-spinner" role="status">
                                <span class="brz-ministryBrands-PrayerButton-visually-hidden">Loading...</span>
                            </div>
                        </div>';
        
        PrayingFormRenderer::renderFormContainer(
            'Submit a Prayer Request',
            true,
            [
                '<button type="button" class="brz-ministryBrands-PrayingForm-btn" data-dismiss="modal">Close</button>',
                '<button id="ekklesia360-prayer-submit" type="submit" name="submit" class="brz-ministryBrands-PrayingForm-btn">' . __('Submit') . '</button>'
            ]
        );
        
        echo '</div>
                </div>
            </form>
        </div>

        <!-- Response Modal -->
        <div id="sfprayerresponse" class="brz-ministryBrands-PrayerButton-response-modal brz-ministryBrands-PrayerButton-modal" tabindex="-1"
             aria-labelledby="responseModalLabel" aria-hidden="true">
            <div class="brz-ministryBrands-PrayerButton-modal-dialog brz-ministryBrands-PrayerButton-modal-dialog-centered">
                <div class="brz-ministryBrands-PrayerButton-modal-content">';
        
        PrayingFormRenderer::renderResponseModalContent();
        
        echo '</div>
            </div>
        </div>

    </div>';
    }

}
