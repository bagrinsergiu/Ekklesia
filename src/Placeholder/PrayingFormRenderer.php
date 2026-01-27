<?php

namespace BrizyEkklesia\Placeholder;

class PrayingFormRenderer
{
    /**
     * Escape HTML output
     */
    private static function escapeHtml($text)
    {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Render the prayer form fields
     */
    public static function renderFormFields()
    {
        ?>
        <div class="brz-ministryBrands-PrayingForm-error-message-container brz-ministryBrands-PrayingForm-mb-3 brz-ministryBrands-PrayingForm-pb-3" style="display:none;"></div>

        <input
                type="text"
                id="name"
                name="name"
                class="brz-ministryBrands-PrayingForm-form-control brz-ministryBrands-PrayingForm-input"
                placeholder="Name"
        />

        <input
                type="email"
                id="email"
                name="email"
                class="brz-ministryBrands-PrayingForm-form-control brz-ministryBrands-PrayingForm-input"
                placeholder="Email"
        />

        <input
                type="tel"
                id="phone"
                name="phone"
                class="brz-ministryBrands-PrayingForm-form-control brz-ministryBrands-PrayingForm-input"
                placeholder="Phone (optional)"
        />

        <textarea
                id="prayer"
                name="prayer"
                class="brz-ministryBrands-PrayingForm-form-control brz-ministryBrands-PrayingForm-textarea"
                placeholder="Prayer"
                rows="3"
        ></textarea>

        <select
                id="syndication"
                name="syndication"
                class="brz-ministryBrands-PrayingForm-form-select brz-ministryBrands-PrayingForm-select"
        >
            <option value="" disabled selected>Public Visibility</option>
            <option value="syndicate publicly">Everything</option>
            <option value="syndicate anonymously">Prayer Only</option>
            <option value="syndicate info only">My Info Only</option>
            <option value="unsyndicated">Make Confidential</option>
        </select>

        <div class="brz-ministryBrands-PrayingForm-radio-group">
            <span class="brz-ministryBrands-PrayingForm-radio-label">Send Anonymously?</span>
            <div class="brz-ministryBrands-PrayingForm-radio-options">
                <div class="brz-ministryBrands-PrayingForm-form-check brz-ministryBrands-PrayingForm-form-check-inline">
                    <input
                            class="brz-ministryBrands-PrayingForm-form-check-input"
                            type="radio"
                            name="authorization"
                            id="authorization_yes"
                            value="authorized anonymously"
                    />
                    <label class="brz-ministryBrands-PrayingForm-form-check-label" for="authorization_yes">
                        Yes
                    </label>
                </div>
                <div class="brz-ministryBrands-PrayingForm-form-check brz-ministryBrands-PrayingForm-form-check-inline">
                    <input
                            class="brz-ministryBrands-PrayingForm-form-check-input"
                            type="radio"
                            name="authorization"
                            id="authorization_no"
                            value="authorized publicly"
                            checked
                    />
                    <label class="brz-ministryBrands-PrayingForm-form-check-label" for="authorization_no">
                        No
                    </label>
                </div>
            </div>
        </div>

        <!-- <div class="brz-ministryBrands-PrayingForm-form-check brz-ministryBrands-PrayingForm-checkbox">
            <input
                    type="checkbox"
                    class="brz-ministryBrands-PrayingForm-form-check-input"
                    id="email_updates-button"
                    name="email_updates"
            />
            <label class="brz-ministryBrands-PrayingForm-form-check-label" for="email_updates-button">
                Email Notifications?
            </label>
        </div> -->
        <hr class="brz-ministryBrands-PrayingForm-footer-divider">
        <?php
    }

    /**
     * Render the complete prayer form with wrapper, form element, and all content
     */
    public static function renderFormContainer($classPrefix = 'brz-ministryBrands-PrayingForm', $headerTitle = 'Submit a Prayer Request', $showCloseButton = false, $footerButtons = [])
    {
        ?>
        <div id="emb_prayerform" class="<?php echo self::escapeHtml($classPrefix); ?>-form">
            <form id="EmbPrayerForm" tabindex="-1" role="dialog" method="post">
                <input type="hidden" name="action" value="ajaxEmbCreatePrayer"/>
                <div class="<?php echo self::escapeHtml($classPrefix); ?>-content brz-p-relative">
                    <div class="brz-p-absolute" style="top: 0; left: 0; right: 0; bottom: 0; display: none; justify-content: center; align-items: center; background: rgba(255,255,255,0.7); z-index: 1051;">
                        <div class="<?php echo self::escapeHtml($classPrefix); ?>-spinner" role="status">
                            <span class="<?php echo self::escapeHtml($classPrefix); ?>-visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="brz-ministryBrands-PrayingForm-container">
                        <!-- Header -->
                        <div class="brz-ministryBrands-PrayingForm-header">
                            <div class="brz-ministryBrands-PrayingForm-title-wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" class="brz-ministryBrands-PrayingForm-icon" viewBox="0 0 16 12.891" fill="currentColor">
                                    <path d="M8.755 1.149a.8.8 0 0 1 .583-.097c.194.049.365.17.462.34l2.917 4.376c.219.316.34.681.34 1.07v1.799c0 .146.097.316.243.365l1.945.632a.78.78 0 0 1 .535.729v2.334c0 .243-.122.486-.316.632s-.438.194-.681.122l-4.084-1.094A3.096 3.096 0 0 1 8.39 9.366V6.473c0-.413.34-.778.778-.778a.8.8 0 0 1 .778.778v1.945c0 .219.17.389.389.389a.4.4 0 0 0 .389-.389V6.376c0-.17-.049-.34-.146-.486L8.487 2.219c-.049-.073-.073-.17-.097-.243a.8.8 0 0 1 0-.34.8.8 0 0 1 .365-.486zm-1.531 0a.8.8 0 0 1 .365.486.8.8 0 0 1 0 .34c-.024.073-.049.17-.097.243L5.4 5.89a.86.86 0 0 0-.122.486v2.042c0 .219.17.389.389.389a.4.4 0 0 0 .389-.389V6.473c0-.413.34-.778.778-.778a.8.8 0 0 1 .778.778v2.893c0 1.41-.948 2.625-2.309 2.99L1.195 13.45c-.243.073-.486.024-.681-.122s-.292-.389-.292-.632v-2.333c0-.316.194-.632.51-.729l1.945-.632c.146-.073.267-.219.267-.389V6.838c0-.389.097-.754.316-1.07l2.918-4.375a.73.73 0 0 1 .802-.34c.097.024.17.049.243.097z"/>
                                </svg>
                                <h5 class="brz-ministryBrands-PrayingForm-title"><?php echo self::escapeHtml($headerTitle); ?></h5>
                            </div>
                            <?php if ($showCloseButton): ?>
                                <button type="button" class="brz-ministryBrands-PrayingForm-close" data-dismiss="modal" aria-label="Close"
                                        style="position: absolute; right: 16px; top: 16px; background: none; border: none; font-size: 24px; cursor: pointer;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            <?php endif; ?>
                        </div>
                        <hr class="brz-ministryBrands-PrayingForm-divider">

                        <!-- Body -->
                        <div class="brz-ministryBrands-PrayingForm-fields">
                            <?php self::renderFormFields(); ?>
                        </div>

                        <!-- Footer -->
                        <?php if (!empty($footerButtons)): ?>
                            <div class="brz-ministryBrands-PrayingForm-footer">
                                <?php foreach ($footerButtons as $button): ?>
                                    <?php echo $button; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * Render the response modal content
     */
    public static function renderResponseModalContent($message = 'Thank you for your prayer request. Your prayer is pending approval.', $buttonText = 'Create Another Prayer Request', $showCloseButton = true)
    {
        ?>
        <div class="brz-ministryBrands-PrayingForm-modal-content">
        <div class="brz-ministryBrands-PrayingForm-modal-header">
            <div class="brz-ministryBrands-PrayingForm-response-title-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" class="brz-ministryBrands-PrayingForm-icon" viewBox="0 0 16 12.891" fill="currentColor">
                    <path d="M8.755 1.149a.8.8 0 0 1 .583-.097c.194.049.365.17.462.34l2.917 4.376c.219.316.34.681.34 1.07v1.799c0 .146.097.316.243.365l1.945.632a.78.78 0 0 1 .535.729v2.334c0 .243-.122.486-.316.632s-.438.194-.681.122l-4.084-1.094A3.096 3.096 0 0 1 8.39 9.366V6.473c0-.413.34-.778.778-.778a.8.8 0 0 1 .778.778v1.945c0 .219.17.389.389.389a.4.4 0 0 0 .389-.389V6.376c0-.17-.049-.34-.146-.486L8.487 2.219c-.049-.073-.073-.17-.097-.243a.8.8 0 0 1 0-.34.8.8 0 0 1 .365-.486zm-1.531 0a.8.8 0 0 1 .365.486.8.8 0 0 1 0 .34c-.024.073-.049.17-.097.243L5.4 5.89a.86.86 0 0 0-.122.486v2.042c0 .219.17.389.389.389a.4.4 0 0 0 .389-.389V6.473c0-.413.34-.778.778-.778a.8.8 0 0 1 .778.778v2.893c0 1.41-.948 2.625-2.309 2.99L1.195 13.45c-.243.073-.486.024-.681-.122s-.292-.389-.292-.632v-2.333c0-.316.194-.632.51-.729l1.945-.632c.146-.073.267-.219.267-.389V6.838c0-.389.097-.754.316-1.07l2.918-4.375a.73.73 0 0 1 .802-.34c.097.024.17.049.243.097z"/>
                </svg>
                <h5 class="brz-ministryBrands-PrayingForm-response-title" id="responseModalHeaderTitle">Prayer Request Submitted</h5>
            </div>
            <?php if ($showCloseButton): ?>
                <button type="button" class="brz-ministryBrands-PrayingForm-modal-close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?php endif; ?>
        </div>
        <div class="brz-ministryBrands-PrayingForm-modal-body brz-ministryBrands-PrayingForm-response-body" style="padding: 1.5rem;">
            <div style="margin-bottom: 0.5rem;">
                <p id="prayer-response-message" class="brz-ministryBrands-PrayingForm-response-message" style="margin-bottom: 1.5rem;">
                    <?php echo self::escapeHtml($message); ?>
                </p>
                <div style="text-align: center;">
                    <button id="prayer-response-button" type="button"
                            class="brz-ministryBrands-PrayingForm-response-button"
                            style="margin-top: 1rem;"
                            data-dismiss="modal">
                        <?php echo self::escapeHtml($buttonText); ?>
                    </button>
                </div>
            </div>
        </div>
        </div>
        <?php
    }
}
