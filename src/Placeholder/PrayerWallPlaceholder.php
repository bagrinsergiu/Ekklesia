<?php

namespace BrizyEkklesia\Placeholder;

use BrizyEkklesia\MonkCms;
use BrizyEkklesia\PrayerCloudApi;
use BrizyPlaceholders\ContentPlaceholder;
use BrizyPlaceholders\ContextInterface;
use BrizyPlaceholders\Replacer;
use Twig_Environment;

class PrayerWallPlaceholder extends PlaceholderAbstract
{
    const NAME = 'ekk_prayer_wall';

    const HTMX_CDN_URL = 'https://cdn.jsdelivr.net/npm/htmx.org@2.0.8/dist/htmx.min.js';

    /** @var bool Emit htmx script only once per page when rendering full HTML */
    private static $htmxScriptEmitted = false;

    /**
     * Endpoint URL for htmx requests (including any required query params, e.g. ?action=...).
     * Set by the host (e.g. WordPress) when registering the placeholder.
     */
    protected $endpointUrl;

    /**
     * @var PrayerCloudApi|null
     */
    protected $prayerCloudApi;

    public function __construct(
        MonkCms $monkCMS,
        Twig_Environment $twig,
        Replacer $replacer = null,
        PrayerCloudApi $prayerCloudApi = null,
        string $endpointUrl = null
    ) {
        parent::__construct($monkCMS, $twig, $replacer);
        $this->prayerCloudApi = $prayerCloudApi;
        $this->endpointUrl = $endpointUrl ?? '';
    }

    /**
     * Parse endpoint URL into base URL (scheme + host + path) and existing query params.
     * Used to build request URLs and to detect AJAX requests.
     */
    private function getEndpointBaseAndParams(): array
    {
        $parsed = parse_url($this->endpointUrl);
        if ($parsed === false) {
            return ['base' => $this->endpointUrl, 'params' => []];
        }
        $base = (isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '')
            . ($parsed['host'] ?? '')
            . (isset($parsed['path']) ? $parsed['path'] : '');
        $params = [];
        if (!empty($parsed['query'])) {
            parse_str($parsed['query'], $params);
        }
        return ['base' => $base, 'params' => $params];
    }

    /**
     * Whether the current request is an AJAX request for this endpoint (based on endpoint URL params).
     */
    private function isEndpointRequest(): bool
    {
        if ($this->endpointUrl === '') {
            return false;
        }
        $endpointParams = $this->getEndpointBaseAndParams()['params'];
        foreach ($endpointParams as $key => $value) {
            if (!isset($_REQUEST[$key]) || (string) $_REQUEST[$key] !== (string) $value) {
                return false;
            }
        }
        return true;
    }

    /**
     * Escape HTML output
     */
    private static function escapeHtml($text)
    {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Escape HTML attribute value (alias for consistency)
     */
    private static function escapeAttr($text)
    {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Build placeholder string from settings
     */
    private function buildPlaceholderString(array $settings): string
    {
        $attrs = [];
        foreach ($settings as $key => $value) {
            if ($key !== 'prayer_category' || $value !== 'all') {
                $attrs[] = $key . "='" . self::escapeAttr($value) . "'";
            }
        }
        return '{{ekk_prayer_wall ' . implode(' ', $attrs) . '}}';
    }
    
    /**
     * Build htmx request URL (merges endpoint params with request params).
     */
    private function buildHtmxUrl(string $placeholderString, int $page, string $instanceId): string
    {
        if ($this->endpointUrl === '') {
            return '#';
        }
        $baseAndParams = $this->getEndpointBaseAndParams();
        $params = array_merge($baseAndParams['params'], [
            'placeholder' => $placeholderString,
            'page' => $page,
            'instance_id' => $instanceId,
        ]);
        return $baseAndParams['base'] . '?' . http_build_query($params);
    }

    /**
     * Build URL for ack (acknowledge) request - used by htmx on the ack button.
     */
    private function buildAckRequestUrl(string $placeholderString, string $prayerId, string $ackLink, int $ackCount): string
    {
        if ($this->endpointUrl === '') {
            return '#';
        }
        $baseAndParams = $this->getEndpointBaseAndParams();
        $params = array_merge($baseAndParams['params'], [
            'placeholder' => $placeholderString,
            'ack_prayer_id' => $prayerId,
            'ack_link' => $ackLink,
            'ack_count' => $ackCount,
        ]);
        return $baseAndParams['base'] . '?' . http_build_query($params);
    }

    public function echoValue(ContextInterface $context, ContentPlaceholder $placeholder)
    {
        // Handle ack (acknowledge) request: call server, return updated button HTML for htmx swap
        if (!empty($_REQUEST['ack_prayer_id']) && !empty($_REQUEST['ack_link'])) {
            $this->handleAckRequest();
            return;
        }
        
        // Generate unique ID for this placeholder instance
        $instanceId = 'prayer-wall-' . uniqid();
        
        $options = [
            'show_name' => true,
            'show_email' => true,
            'show_phone' => true,
            'show_prayer_content' => true,
            'show_date' => true,
            'show_acknowledgment_count' => true,
            'show_acknowledgment_button' => true,
            'show_category' => true,
            'show_prayer_request_button' => true,
            'prayers_per_page' => 5,
            'overflow_behavior' => 'pagination',
            'privacy_settings' => 'submitter_choice',
            'prayer_category' => 'all',
        ];

        $settings = array_merge($options, $placeholder->getAttributes());

        $show_name = (bool) $settings['show_name'];
        $show_email = (bool) $settings['show_email'];
        $show_phone = (bool) $settings['show_phone'];
        $show_prayer_content = (bool) $settings['show_prayer_content'];
        $show_date = (bool) $settings['show_date'];
        $show_acknowledgment_count = (bool) $settings['show_acknowledgment_count'];
        $show_acknowledgment_button = (bool) $settings['show_acknowledgment_button'];
        $show_category = (bool) $settings['show_category'];
        $show_prayer_request_button = (bool) $settings['show_prayer_request_button'];
        $prayers_per_page = max(1, (int) $settings['prayers_per_page']);
        $overflow_behavior = $settings['overflow_behavior'];
        $privacy_settings = $settings['privacy_settings'];
        $selected_category = $settings['prayer_category'] ?? 'all';

        // Get page from request (for htmx pagination)
        $requestedPage = isset($_REQUEST['page']) ? max(1, (int) $_REQUEST['page']) : 1;
        $isAjaxRequest = $this->isEndpointRequest();

        $prayers = [];
        $totalPages = 1;
        $currentPage = $requestedPage;

        if ($this->prayerCloudApi !== null && $this->prayerCloudApi->isConnected()) {
            $prayersData = $this->prayerCloudApi->getPrayers(
                $prayers_per_page,
                $requestedPage,
                'approved',
                $selected_category
            );

            if ($prayersData && isset($prayersData->meta) && $prayersData->meta->total > 0) {
                $currentPage = (int) $prayersData->meta->current_page;
                $totalPages = (int) ceil($prayersData->meta->total / $prayers_per_page);

                foreach ($prayersData->data as $item) {
                    $dateStr = '';
                    if (isset($item->meta->posted_at->date)) {
                        $dateObj = date_create($item->meta->posted_at->date);
                        $dateStr = $dateObj ? date_format($dateObj, 'F j, Y - g:i a') : $item->meta->posted_at->date;
                    }

                    $categoryNames = [];
                    if (isset($item->data->tags->category)) {
                        $cats = is_array($item->data->tags->category)
                            ? $item->data->tags->category
                            : [$item->data->tags->category];
                        foreach ($cats as $cat) {
                            if (isset($cat->name)) {
                                $categoryNames[] = $cat->name;
                            }
                        }
                    }

                    $prayers[] = [
                        'name' => $item->data->name ?? '',
                        'email' => $item->data->email ?? '',
                        'phone' => $item->data->phone ?? '',
                        'prayer' => $item->data->prayer ?? '',
                        'date' => $dateStr,
                        'categories' => $categoryNames,
                        'category' => !empty($categoryNames) ? implode(', ', $categoryNames) : '',
                        'ackCount' => $item->data->acknowledgment_count ?? 0,
                        'ackLink' => $item->links->acknowledge_prayer_link ?? '#',
                        'uuid' => $item->data->uuid ?? '',
                    ];
                }
            }
        }

        // Build placeholder string for htmx requests (needed for both AJAX and non-AJAX)
        $placeholderString = $this->buildPlaceholderString($settings);
        
        // If AJAX request, return prayer cards HTML and pagination
        if ($isAjaxRequest) {
            // Get instance ID from request if available, otherwise generate new one
            $requestInstanceId = isset($_REQUEST['instance_id']) ? filter_var($_REQUEST['instance_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : $instanceId;
            
            // For lazy loading, return only the cards HTML (no wrapper) so htmx can append directly
            // For regular pagination, return cards wrapped in container div
            $isLazyLoad = ($overflow_behavior === 'lazy_load');
            
            if (!$isLazyLoad) {
                echo '<div id="' . self::escapeAttr($requestInstanceId . '__cards') . '" class="brz-ministryBrandsPrayerWall__cards">';
                // Loading overlay for regular pagination (shown during request)
                $this->renderLoadingOverlay($requestInstanceId);
            }
            
            $this->renderPrayerCards($prayers, $settings, $privacy_settings, $placeholderString);
            
            if (!$isLazyLoad) {
                echo '</div>';
            }
            
            // Always update pagination
            if ($totalPages > 1) {
                $this->renderPagination($totalPages, $currentPage, $prayers_per_page, $overflow_behavior, $placeholderString, true, $requestInstanceId);
            }
            return;
        }

        // Load htmx once per page (only when rendering full HTML, not on AJAX)
        if (!self::$htmxScriptEmitted) {
            echo '<script src="' . self::escapeAttr(self::HTMX_CDN_URL) . '" defer></script>';
            self::$htmxScriptEmitted = true;
        }
        
        // Start main container
        echo '<div class="brz-ministryBrandsPrayerWall__container">';

        // Prayer Request Button
        if ($show_prayer_request_button) {
            echo '<div class="brz-ministryBrandsPrayerWall__request-button-wrapper">
                <button class="brz-ministryBrandsPrayerWall__request-button" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="brz-ministryBrandsPrayerWall__request-button-icon" viewBox="0 0 16 12.891" fill="currentColor">
                        <path d="M8.755 1.149a.8.8 0 0 1 .583-.097c.194.049.365.17.462.34l2.917 4.376c.219.316.34.681.34 1.07v1.799c0 .146.097.316.243.365l1.945.632a.78.78 0 0 1 .535.729v2.334c0 .243-.122.486-.316.632s-.438.194-.681.122l-4.084-1.094A3.096 3.096 0 0 1 8.39 9.366V6.473c0-.413.34-.778.778-.778a.8.8 0 0 1 .778.778v1.945c0 .219.17.389.389.389a.4.4 0 0 0 .389-.389V6.376c0-.17-.049-.34-.146-.486L8.487 2.219c-.049-.073-.073-.17-.097-.243a.8.8 0 0 1 0-.34.8.8 0 0 1 .365-.486zm-1.531 0a.8.8 0 0 1 .365.486.8.8 0 0 1 0 .34c-.024.073-.049.17-.097.243L5.4 5.89a.86.86 0 0 0-.122.486v2.042c0 .219.17.389.389.389a.4.4 0 0 0 .389-.389V6.473c0-.413.34-.778.778-.778a.8.8 0 0 1 .778.778v2.893c0 1.41-.948 2.625-2.309 2.99L1.195 13.45c-.243.073-.486.024-.681-.122s-.292-.389-.292-.632v-2.333c0-.316.194-.632.51-.729l1.945-.632c.146-.073.267-.219.267-.389V6.838c0-.389.097-.754.316-1.07l2.918-4.375a.73.73 0 0 1 .802-.34c.097.024.17.049.243.097z"/>
                    </svg>
                    Create Prayer Request
                </button>
            </div>';
        }

        // Prayer cards container with unique ID
        echo '<div id="' . self::escapeAttr($instanceId . '__cards') . '" class="brz-ministryBrandsPrayerWall__cards">';
        // Loading overlay (shown during htmx requests)
        $this->renderLoadingOverlay($instanceId);
        // Prayer cards content
        $this->renderPrayerCards($prayers, $settings, $privacy_settings, $placeholderString);
        echo '</div>'; // cards container

        // Pagination / Lazy Load
        if ($totalPages > 1) {
            $this->renderPagination($totalPages, $currentPage, $prayers_per_page, $overflow_behavior, $placeholderString, false, $instanceId);
        }

        echo '</div>'; // main container

        // Prayer Form Modal
        if ($show_prayer_request_button) {
            echo '<div class="brz-ministryBrands-PrayerWall-modal">';
            echo '<div class="brz-ministryBrands-PrayerWall-modal-backdrop"></div>';
            echo '<div class="brz-ministryBrands-PrayerWall-modal-dialog">';
            echo '<div class="brz-ministryBrands-PrayerWall-modal-content">';

            echo '<div class="brz-ministryBrands-PrayerWall-form">';

            PrayingFormRenderer::renderFormContainer(
                'brz-ministryBrands-PrayerWall',
                'Submit a Prayer Request',
                true,
                [
                    '<button type="button" class="brz-ministryBrands-PrayingForm-btn" data-dismiss="modal">Close</button>',
                    '<button id="ekklesia360-prayer-submit" type="submit" name="submit" class="brz-ministryBrands-PrayingForm-btn">Submit</button>'
                ]
            );

            echo '</div>'; // form

            echo '<!-- Response Section -->
            <div id="sfprayerresponse" class="brz-ministryBrands-PrayerWall-response" style="display: none;">';

            PrayingFormRenderer::renderResponseModalContent();

            echo '</div>';

            echo '</div>'; // modal-content
            echo '</div>'; // modal-dialog
            echo '</div>'; // modal
        }
    }

    /**
     * Render prayer cards HTML
     *
     * @param array $prayers
     * @param array $settings
     * @param string $privacy_settings
     * @param string $placeholderString Used to build ack request URL for htmx
     */
    private function renderPrayerCards(array $prayers, array $settings, string $privacy_settings, string $placeholderString = '')
    {
        $show_name = (bool) $settings['show_name'];
        $show_email = (bool) $settings['show_email'];
        $show_phone = (bool) $settings['show_phone'];
        $show_prayer_content = (bool) $settings['show_prayer_content'];
        $show_date = (bool) $settings['show_date'];
        $show_acknowledgment_count = (bool) $settings['show_acknowledgment_count'];
        $show_acknowledgment_button = (bool) $settings['show_acknowledgment_button'];
        $show_category = (bool) $settings['show_category'];
        $selected_category = $settings['prayer_category'] ?? 'all';

        if (empty($prayers)) {
            $noResultsMsg = ($selected_category !== 'all' && !empty($selected_category))
                ? 'No prayer requests found for this category.'
                : 'There are no prayers at this time.';
            echo '<div class="brz-ministryBrandsPrayerWall__no-results">' . self::escapeHtml($noResultsMsg) . '</div>';
        } else {
            foreach ($prayers as $prayer) {
                $displayName = $prayer['name'];
                if ($privacy_settings === 'all_anonymous') {
                    $displayName = 'Anonymous';
                }

                echo '<div class="brz-ministryBrandsPrayerWall__card">';
                echo '<div class="brz-ministryBrandsPrayerWall__card-body">';

                // Category badges
                if ($show_category) {
                    $categories = !empty($prayer['categories']) ? $prayer['categories'] : (!empty($prayer['category']) ? [$prayer['category']] : []);
                    if (!empty($categories)) {
                        echo '<div class="brz-ministryBrandsPrayerWall__category">';
                        foreach ($categories as $catName) {
                            echo '<span class="brz-ministryBrandsPrayerWall__badge">' . self::escapeHtml($catName) . '</span>';
                        }
                        echo '</div>';
                    }
                }

                // Name and Date
                if ($show_name || $show_date) {
                    echo '<div class="brz-ministryBrandsPrayerWall__title">';

                    if ($show_name) {
                        echo '<span class="brz-ministryBrandsPrayerWall__name">' . self::escapeHtml($displayName) . '</span>';
                    }

                    if ($show_date && !empty($prayer['date'])) {
                        echo '<span class="brz-ministryBrandsPrayerWall__date">' . self::escapeHtml($prayer['date']) . '</span>';
                    }

                    echo '</div>';
                }

                // Prayer content
                if ($show_prayer_content && !empty($prayer['prayer'])) {
                    echo '<div class="brz-ministryBrandsPrayerWall__content">';
                    echo '<p>' . self::escapeHtml($prayer['prayer']) . '</p>';
                    echo '</div>';
                }

                echo '</div>'; // card-body

                // Card footer
                $hasFooterLeft = $show_acknowledgment_button;
                $hasFooterRight = $show_phone || $show_email;

                if ($hasFooterLeft || $hasFooterRight) {
                    echo '<div class="brz-ministryBrandsPrayerWall__card-footer">';

                    // Acknowledgment button, share dropdown, reply
                    if ($show_acknowledgment_button) {
                        $ackLink = $prayer['ackLink'] ?? '#';
                        $ackLinkEsc = self::escapeHtml($ackLink);
                        $prayerId = self::escapeHtml($prayer['uuid']);
                        $ackCount = (int) ($prayer['ackCount'] ?? 0);

                        echo '<div class="brz-ministryBrandsPrayerWall__footer-left">';

                        // Acknowledge button (htmx: request goes to placeholder, server increments and returns updated button)
                        if ($placeholderString !== '') {
                            $ackRequestUrl = $this->buildAckRequestUrl($placeholderString, $prayer['uuid'], $ackLink, $ackCount);
                            $this->renderAckButtonHtml($prayer['uuid'], $ackCount, $ackRequestUrl, $show_acknowledgment_count, false);
                        } else {
                            echo '<a href="#" class="brz-ministryBrandsPrayerWall__ack-button" data-prayer-id="' . $prayerId . '" data-link="' . $ackLinkEsc . '">';
                            echo '<svg xmlns="http://www.w3.org/2000/svg" class="brz-ministryBrandsPrayerWall__ack-icon" viewBox="0 0 16 12.891" fill="currentColor">';
                            echo '<path d="M8.755 1.149a.8.8 0 0 1 .583-.097c.194.049.365.17.462.34l2.917 4.376c.219.316.34.681.34 1.07v1.799c0 .146.097.316.243.365l1.945.632a.78.78 0 0 1 .535.729v2.334c0 .243-.122.486-.316.632s-.438.194-.681.122l-4.084-1.094A3.096 3.096 0 0 1 8.39 9.366V6.473c0-.413.34-.778.778-.778a.8.8 0 0 1 .778.778v1.945c0 .219.17.389.389.389a.4.4 0 0 0 .389-.389V6.376c0-.17-.049-.34-.146-.486L8.487 2.219c-.049-.073-.073-.17-.097-.243a.8.8 0 0 1 0-.34.8.8 0 0 1 .365-.486zm-1.531 0a.8.8 0 0 1 .365.486.8.8 0 0 1 0 .34c-.024.073-.049.17-.097.243L5.4 5.89a.86.86 0 0 0-.122.486v2.042c0 .219.17.389.389.389a.4.4 0 0 0 .389-.389V6.473c0-.413.34-.778.778-.778a.8.8 0 0 1 .778.778v2.893c0 1.41-.948 2.625-2.309 2.99L1.195 13.45c-.243.073-.486.024-.681-.122s-.292-.389-.292-.632v-2.333c0-.316.194-.632.51-.729l1.945-.632c.146-.073.267-.219.267-.389V6.838c0-.389.097-.754.316-1.07l2.918-4.375a.73.73 0 0 1 .802-.34c.097.024.17.049.243.097z"/>';
                            echo '</svg>';
                            if ($show_acknowledgment_count) {
                                echo '<span class="brz-ministryBrandsPrayerWall__ack-count">' . $ackCount . '</span>';
                            }
                            echo '</a>';
                        }

                        // Share dropdown
                        echo self::renderShareDropdown($prayer);

                        // Reply button
                        echo self::renderReplyButton($prayer);

                        echo '</div>';
                    }

                    // Contact info
                    if ($hasFooterRight) {
                        echo '<div class="brz-ministryBrandsPrayerWall__footer-right">';

                        if ($show_phone && !empty($prayer['phone'])) {
                            echo '<span class="brz-ministryBrandsPrayerWall__phone">';
                            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="brz-ministryBrandsPrayerWall__contact-icon" fill="currentColor"><path d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z"/></svg>';
                            echo self::escapeHtml($prayer['phone']);
                            echo '</span>';
                        }

                        if ($show_email && !empty($prayer['email'])) {
                            echo '<span class="brz-ministryBrandsPrayerWall__email">';
                            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="brz-ministryBrandsPrayerWall__contact-icon" fill="currentColor"><path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>';
                            echo self::escapeHtml($prayer['email']);
                            echo '</span>';
                        }

                        echo '</div>';
                    }

                    echo '</div>'; // card-footer
                }

                echo '</div>'; // card
            }
        }
    }

    /**
     * Render pagination controls
     *
     * @param int $totalPages
     * @param int $currentPage
     * @param int $prayers_per_page
     * @param string $overflow_behavior
     * @param string $placeholderString
     * @param bool $isAjaxRequest
     * @param string $instanceId
     */
    private function renderPagination(int $totalPages, int $currentPage, int $prayers_per_page, string $overflow_behavior, string $placeholderString, bool $isAjaxRequest = false, string $instanceId = '')
    {
        $instanceId = $instanceId ?: 'prayer-wall-' . uniqid();
        $paginationId = $instanceId . '__pagination';
        $cardsId = $instanceId . '__cards';
        $swapOob = $isAjaxRequest ? ' hx-swap-oob="true"' : '';
        
        echo '<div id="' . self::escapeAttr($paginationId) . '" class="brz-ministryBrandsPrayerWall__pagination"' . $swapOob . '>';
        
        if ($overflow_behavior === 'lazy_load') {
            $this->renderLazyLoadPagination($totalPages, $currentPage, $prayers_per_page, $placeholderString, $instanceId, $cardsId);
        } else {
            $this->renderRegularPagination($totalPages, $currentPage, $placeholderString, $instanceId, $cardsId);
        }
        
        echo '</div>';
    }
    
    /**
     * Render lazy load pagination (Show More button)
     */
    private function renderLazyLoadPagination(int $totalPages, int $currentPage, int $prayers_per_page, string $placeholderString, string $instanceId, string $cardsId): void
    {
        if ($currentPage >= $totalPages) {
            return; // No button needed if we've reached the last page
        }
        
        $nextPage = $currentPage + 1;
        $url = $this->buildHtmxUrl($placeholderString, $nextPage, $instanceId);
        $overlayId = '#' . $instanceId . '__loading-overlay';
        
        echo '<button 
            class="brz-ministryBrandsPrayerWall__load-more-btn" 
            hx-get="' . self::escapeAttr($url) . '"
            hx-target="#' . self::escapeAttr($cardsId) . '"
            hx-swap="beforeend"
            hx-indicator="' . self::escapeAttr($overlayId) . '"
        >Show Next ' . $prayers_per_page . '</button>';
    }
    
    /**
     * Render regular pagination (page numbers)
     */
    private function renderRegularPagination(int $totalPages, int $currentPage, string $placeholderString, string $instanceId, string $cardsId): void
    {
        $overlayId = '#' . $instanceId . '__loading-overlay';
        
        echo '<nav>';
        echo '<ul class="brz-ministryBrandsPrayerWall__pagination-list">';
        
        // Previous button
        echo '<li class="brz-ministryBrandsPrayerWall__pagination-item">';
        if ($currentPage > 1) {
            $url = $this->buildHtmxUrl($placeholderString, $currentPage - 1, $instanceId);
            echo '<button class="brz-ministryBrandsPrayerWall__pagination-link" 
                hx-get="' . self::escapeAttr($url) . '"
                hx-target="#' . self::escapeAttr($cardsId) . '"
                hx-swap="innerHTML"
                hx-indicator="' . self::escapeAttr($overlayId) . '"
            >&laquo;</button>';
        } else {
            echo '<button class="brz-ministryBrandsPrayerWall__pagination-link" disabled>&laquo;</button>';
        }
        echo '</li>';
        
        // Page numbers
        for ($i = 1; $i <= $totalPages; $i++) {
            $activeClass = ($i === $currentPage) ? ' brz-ministryBrandsPrayerWall__pagination-link--active' : '';
            echo '<li class="brz-ministryBrandsPrayerWall__pagination-item">';
            
            if ($i === $currentPage) {
                echo '<button class="brz-ministryBrandsPrayerWall__pagination-link' . $activeClass . '" disabled>' . $i . '</button>';
            } else {
                $url = $this->buildHtmxUrl($placeholderString, $i, $instanceId);
                echo '<button class="brz-ministryBrandsPrayerWall__pagination-link' . $activeClass . '" 
                    hx-get="' . self::escapeAttr($url) . '"
                    hx-target="#' . self::escapeAttr($cardsId) . '"
                    hx-swap="innerHTML"
                    hx-indicator="' . self::escapeAttr($overlayId) . '"
                >' . $i . '</button>';
            }
            echo '</li>';
        }
        
        // Next button
        echo '<li class="brz-ministryBrandsPrayerWall__pagination-item">';
        if ($currentPage < $totalPages) {
            $url = $this->buildHtmxUrl($placeholderString, $currentPage + 1, $instanceId);
            echo '<button class="brz-ministryBrandsPrayerWall__pagination-link" 
                hx-get="' . self::escapeAttr($url) . '"
                hx-target="#' . self::escapeAttr($cardsId) . '"
                hx-swap="innerHTML"
                hx-indicator="' . self::escapeAttr($overlayId) . '"
            >&raquo;</button>';
        } else {
            echo '<button class="brz-ministryBrandsPrayerWall__pagination-link" disabled>&raquo;</button>';
        }
        echo '</li>';
        
        echo '</ul>';
        echo '</nav>';
    }
    
    /**
     * Handle ack request: call external ack URL, then output updated button HTML for htmx swap
     */
    private function handleAckRequest(): void
    {
        $prayerId = isset($_REQUEST['ack_prayer_id']) ? filter_var($_REQUEST['ack_prayer_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
        $ackLink = isset($_REQUEST['ack_link']) ? filter_var($_REQUEST['ack_link'], FILTER_SANITIZE_URL) : '';
        $currentCount = isset($_REQUEST['ack_count']) ? max(0, (int) $_REQUEST['ack_count']) : 0;
        $placeholderString = isset($_REQUEST['placeholder']) ? stripslashes((string) $_REQUEST['placeholder']) : '';

        if ($prayerId === '' || $ackLink === '' || $ackLink === '#') {
            return;
        }

        $newCount = $currentCount;
        if (function_exists('wp_remote_get')) {
            $response = wp_remote_get($ackLink, ['timeout' => 15]);
            if (!is_wp_error($response)) {
                $code = wp_remote_retrieve_response_code($response);
                $body = wp_remote_retrieve_body($response);
                if ($code >= 200 && $code < 300) {
                    $newCount = $this->parseAckCountFromResponse($body, $currentCount);
                }
            }
        } else {
            $context = stream_context_create(['http' => ['timeout' => 15]]);
            $body = @file_get_contents($ackLink, false, $context);
            if ($body !== false) {
                $newCount = $this->parseAckCountFromResponse($body, $currentCount);
            }
        }

        $ackRequestUrl = $this->buildAckRequestUrl($placeholderString, $prayerId, $ackLink, $newCount);
        $this->renderAckButtonHtml($prayerId, $newCount, $ackRequestUrl, true, true);
    }

    /**
     * Parse updated acknowledgment count from API response (JSON or HTML)
     */
    private function parseAckCountFromResponse(string $body, int $fallback): int
    {
        $decoded = @json_decode($body, true);
        if (is_array($decoded)) {
            $count = $decoded['count'] ?? $decoded['ackCount'] ?? $decoded['acknowledgment_count'] ?? $decoded['acknowledgments'] ?? null;
            if ($count !== null && is_numeric($count)) {
                return (int) $count;
            }
        }
        return $fallback + 1;
    }

    /**
     * Render the ack button HTML (with optional htmx attributes for server-driven increment)
     */
    private function renderAckButtonHtml(string $prayerId, int $ackCount, string $ackRequestUrl, bool $showCount, bool $acknowledged = false): void
    {
        $btnId = 'ack-btn-' . self::escapeAttr($prayerId);
        $classes = 'brz-ministryBrandsPrayerWall__ack-button';
        if ($acknowledged) {
            $classes .= ' brz-ministryBrandsPrayerWall__ack-button--acknowledged';
        }
        echo '<a id="' . $btnId . '" href="#" class="' . $classes . '" data-prayer-id="' . self::escapeAttr($prayerId) . '"';
        echo ' hx-get="' . self::escapeAttr($ackRequestUrl) . '" hx-target="this" hx-swap="outerHTML"';
        echo '>';
        echo '<svg xmlns="http://www.w3.org/2000/svg" class="brz-ministryBrandsPrayerWall__ack-icon" viewBox="0 0 16 12.891" fill="currentColor">';
        echo '<path d="M8.755 1.149a.8.8 0 0 1 .583-.097c.194.049.365.17.462.34l2.917 4.376c.219.316.34.681.34 1.07v1.799c0 .146.097.316.243.365l1.945.632a.78.78 0 0 1 .535.729v2.334c0 .243-.122.486-.316.632s-.438.194-.681.122l-4.084-1.094A3.096 3.096 0 0 1 8.39 9.366V6.473c0-.413.34-.778.778-.778a.8.8 0 0 1 .778.778v1.945c0 .219.17.389.389.389a.4.4 0 0 0 .389-.389V6.376c0-.17-.049-.34-.146-.486L8.487 2.219c-.049-.073-.073-.17-.097-.243a.8.8 0 0 1 0-.34.8.8 0 0 1 .365-.486zm-1.531 0a.8.8 0 0 1 .365.486.8.8 0 0 1 0 .34c-.024.073-.049.17-.097.243L5.4 5.89a.86.86 0 0 0-.122.486v2.042c0 .219.17.389.389.389a.4.4 0 0 0 .389-.389V6.473c0-.413.34-.778.778-.778a.8.8 0 0 1 .778.778v2.893c0 1.41-.948 2.625-2.309 2.99L1.195 13.45c-.243.073-.486.024-.681-.122s-.292-.389-.292-.632v-2.333c0-.316.194-.632.51-.729l1.945-.632c.146-.073.267-.219.267-.389V6.838c0-.389.097-.754.316-1.07l2.918-4.375a.73.73 0 0 1 .802-.34c.097.024.17.049.243.097z"/>';
        echo '</svg>';
        if ($showCount) {
            echo '<span class="brz-ministryBrandsPrayerWall__ack-count">' . (int) $ackCount . '</span>';
        }
        echo '</a>';
    }

    /**
     * Render loading overlay (shown during htmx requests, replaces content)
     */
    private function renderLoadingOverlay(string $instanceId): void
    {
        $overlayId = $instanceId . '__loading-overlay';
        echo '<div id="' . self::escapeAttr($overlayId) . '" class="brz-ministryBrandsPrayerWall__loading-overlay htmx-indicator" aria-hidden="true">';
        echo '<div class="brz-ministryBrandsPrayerWall__loading-overlay-content">';
        echo '<span class="brz-ministryBrandsPrayerWall__loading-spinner"></span>';
        echo '<p class="brz-ministryBrandsPrayerWall__loading-text">Loading prayers...</p>';
        echo '</div>';
        echo '</div>';
    }

    /**
     * Render the share dropdown for a prayer card.
     *
     * @param array $prayer
     *
     * @return string
     */
    private static function renderShareDropdown(array $prayer)
    {
        $uuid = self::escapeHtml($prayer['uuid'] ?? '');
        $text = self::escapeHtml($prayer['prayer'] ?? '');
        $url = 'prayer-cloud/' . $uuid;

        $encodedUrl = urlencode($url);
        $encodedText = urlencode($prayer['prayer'] ?? '');

        return '
        <div class="brz-ministryBrandsPrayerWall__share-dropdown">
            <button
                class="brz-ministryBrandsPrayerWall__share-button"
                type="button"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="brz-ministryBrandsPrayerWall__share-icon" viewBox="0 0 448 512" fill="currentColor"><path d="M352 224c53 0 96-43 96-96s-43-96-96-96s-96 43-96 96c0 4 .2 8 .7 11.9l-94.1 47C145.4 170.2 121.9 160 96 160c-53 0-96 43-96 96s43 96 96 96c25.9 0 49.4-10.2 66.6-26.9l94.1 47c-.5 3.9-.7 7.8-.7 11.9c0 53 43 96 96 96s96-43 96-96s-43-96-96-96c-25.9 0-49.4 10.2-66.6 26.9l-94.1-47c.5-3.9 .7-7.8 .7-11.9s-.2-8-.7-11.9l94.1-47C302.6 213.8 326.1 224 352 224z"/></svg>
                Share
            </button>
            <ul class="brz-ministryBrandsPrayerWall__share-menu">
                <li><a class="brz-ministryBrandsPrayerWall__share-item" href="https://www.facebook.com/dialog/feed?app_id=184683071273&link=' . $encodedUrl . '&name=Prayer&caption=' . $encodedText . '&redirect_uri=http%3A%2F%2Fwww.facebook.com%2F" target="_blank" rel="noopener">Facebook</a></li>
                <li><a class="brz-ministryBrandsPrayerWall__share-item" href="http://pinterest.com/pin/create/button/?url=' . $encodedUrl . '&media=' . $encodedUrl . '&description=' . $encodedText . '" target="_blank" rel="noopener">Pinterest</a></li>
                <li><a class="brz-ministryBrandsPrayerWall__share-item" href="http://twitter.com/intent/tweet?text=' . $encodedText . '" target="_blank" rel="noopener">Twitter</a></li>
                <li><a class="brz-ministryBrandsPrayerWall__share-item" href="mailto:?subject=Please%20Pray%20For%20Us!&body=' . $encodedText . '">Email</a></li>
            </ul>
        </div>';
    }

    /**
     * Render the reply button for a prayer card.
     *
     * @param array $prayer
     *
     * @return string
     */
    private static function renderReplyButton(array $prayer)
    {
        $email = $prayer['email'] ?? '';

        if (empty($email)) {
            return '';
        }

        $mailto = 'mailto:' . self::escapeHtml($email) . '?subject=Response%20to%20your%20prayer!';

        return '
        <a class="brz-ministryBrandsPrayerWall__reply-button" href="' . $mailto . '">
            <svg xmlns="http://www.w3.org/2000/svg" class="brz-ministryBrandsPrayerWall__reply-icon" viewBox="0 0 512 512" fill="currentColor"><path d="M205 34.8c11.5 5.1 19 16.6 19 29.2l0 64 112 0C399.4 128 448 176.6 448 240l0 48c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-48c0-26.5-21.5-48-48-48l-112 0 0 64c0 12.6-7.4 24.1-19 29.2s-25 3-34.4-5.4l-160-144C3.9 129.5 0 121 0 112s3.9-17.5 10.6-23.8l160-144c9.4-8.5 22.9-10.6 34.4-5.4z"/></svg>
            <span>Reply</span>
        </a>';
    }
}
