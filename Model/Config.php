<?php
declare(strict_types=1);

namespace Basecom\SpeculationRulesToolbox\Model;

use Basecom\SpeculationRulesToolbox\Api\Data\ConfigInterface;
use Basecom\SpeculationRulesToolbox\Model\Config\Source\ApplicationScopes;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

// Disable PHPCS, as forced short descriptions on getters are redundant
// phpcs:disable
class Config implements ConfigInterface
{
    /**
     * Use 'prefetch' as the default value, as it is more conservative than 'prerender'
     */
    private const DEFAULT_PRELOAD_TYPE = 'prefetch';
    private const DEFAULT_CONCURRENT_PRELOADS = '6,2';
    private const DEFAULT_OBSERVER_DELAY = 300;
    private const DEFAULT_OBSERVER_THRESHOLD = 50;

    /**
     * Configuration
     */
    private const CONFIG_PATH_SPECULATION_RULES_ENABLED =
        'basecom_performance/speculation_rules_toolbox/enabled';
    private const CONFIG_PATH_SPECULATION_RULES_BANNED_TERMS =
        'basecom_performance/speculation_rules_toolbox/banned_terms';
    private const CONFIG_PATH_SPECULATION_RULES_MODERATE_ENABLED =
        'basecom_performance/speculation_rules_toolbox/moderate_enabled';
    private const CONFIG_PATH_SPECULATION_RULES_PRELOAD_TYPE =
        'basecom_performance/speculation_rules_toolbox/preload_type';

    /**
     * Settings for all pages
     */
    private const CONFIG_PATH_SPECULATION_RULES_ALL_PAGES_CUSTOM_LINKS_ENABLED =
        'basecom_performance/speculation_rules_toolbox/all_pages/custom_links_enabled';
    private const CONFIG_PATH_SPECULATION_RULES_ALL_PAGES_PRELOAD_TYPE_CUSTOM =
        'basecom_performance/speculation_rules_toolbox/all_pages/preload_type_custom';
    private const CONFIG_PATH_SPECULATION_RULES_ALL_PAGES_CUSTOM_LINKS =
        'basecom_performance/speculation_rules_toolbox/all_pages/custom_links';
    private const CONFIG_PATH_SPECULATION_RULES_ALL_PAGES_CUSTOM_SCRIPT_ENABLED =
        'basecom_performance/speculation_rules_toolbox/all_pages/custom_script_enabled';
    private const CONFIG_PATH_SPECULATION_RULES_ALL_PAGES_CUSTOM_SCRIPT =
        'basecom_performance/speculation_rules_toolbox/all_pages/custom_script';

    /**
     * Homepage Configuration
     */
    private const CONFIG_PATH_SPECULATION_RULES_HOMEPAGE_CUSTOM_LINKS_ENABLED =
        'basecom_performance/speculation_rules_toolbox/homepage/custom_links_enabled';
    private const CONFIG_PATH_SPECULATION_RULES_HOMEPAGE_PRELOAD_TYPE_CUSTOM =
        'basecom_performance/speculation_rules_toolbox/homepage/preload_type_custom';
    private const CONFIG_PATH_SPECULATION_RULES_HOMEPAGE_CUSTOM_LINKS =
        'basecom_performance/speculation_rules_toolbox/homepage/custom_links';
    private const CONFIG_PATH_SPECULATION_RULES_HOMEPAGE_DYNAMIC_TARGETS_ENABLED =
        'basecom_performance/speculation_rules_toolbox/homepage/dynamic_targets_enabled';
    private const CONFIG_PATH_SPECULATION_RULES_HOMEPAGE_PRELOAD_TYPE_DYNAMIC =
        'basecom_performance/speculation_rules_toolbox/homepage/preload_type_dynamic';
    private const CONFIG_PATH_SPECULATION_RULES_HOMEPAGE_CSS_SELECTOR =
        'basecom_performance/speculation_rules_toolbox/homepage/css_selector';
    private const CONFIG_PATH_SPECULATION_RULES_HOMEPAGE_CONCURRENT_PRELOADS =
        'basecom_performance/speculation_rules_toolbox/homepage/concurrent_preloads';

    /**
     * PLP Configuration
     */
    private const CONFIG_PATH_SPECULATION_RULES_PLP_CUSTOM_LINKS_ENABLED =
        'basecom_performance/speculation_rules_toolbox/plp/custom_links_enabled';
    private const CONFIG_PATH_SPECULATION_RULES_PLP_PRELOAD_TYPE_CUSTOM =
        'basecom_performance/speculation_rules_toolbox/plp/preload_type_custom';
    private const CONFIG_PATH_SPECULATION_RULES_PLP_CUSTOM_LINKS =
        'basecom_performance/speculation_rules_toolbox/plp/custom_links';
    private const CONFIG_PATH_SPECULATION_RULES_PLP_DYNAMIC_TARGETS_ENABLED =
        'basecom_performance/speculation_rules_toolbox/plp/dynamic_targets_enabled';
    private const CONFIG_PATH_SPECULATION_RULES_PLP_PRELOAD_TYPE_DYNAMIC =
        'basecom_performance/speculation_rules_toolbox/plp/preload_type_dynamic';
    private const CONFIG_PATH_SPECULATION_RULES_PLP_CSS_SELECTOR =
        'basecom_performance/speculation_rules_toolbox/plp/css_selector';
    private const CONFIG_PATH_SPECULATION_RULES_PLP_CONCURRENT_PRELOADS =
        'basecom_performance/speculation_rules_toolbox/plp/concurrent_preloads';

    /**
     * PDP Configuration
     */
    private const CONFIG_PATH_SPECULATION_RULES_PDP_CUSTOM_LINKS_ENABLED =
        'basecom_performance/speculation_rules_toolbox/pdp/custom_links_enabled';
    private const CONFIG_PATH_SPECULATION_RULES_PDP_PRELOAD_TYPE_CUSTOM =
        'basecom_performance/speculation_rules_toolbox/pdp/preload_type_custom';
    private const CONFIG_PATH_SPECULATION_RULES_PDP_CUSTOM_LINKS =
        'basecom_performance/speculation_rules_toolbox/pdp/custom_links';
    private const CONFIG_PATH_SPECULATION_RULES_PDP_DYNAMIC_TARGETS_ENABLED =
        'basecom_performance/speculation_rules_toolbox/pdp/dynamic_targets_enabled';
    private const CONFIG_PATH_SPECULATION_RULES_PDP_PRELOAD_TYPE_DYNAMIC =
        'basecom_performance/speculation_rules_toolbox/pdp/preload_type_dynamic';
    private const CONFIG_PATH_SPECULATION_RULES_PDP_CSS_SELECTOR =
        'basecom_performance/speculation_rules_toolbox/pdp/css_selector';
    private const CONFIG_PATH_SPECULATION_RULES_PDP_CONCURRENT_PRELOADS =
        'basecom_performance/speculation_rules_toolbox/pdp/concurrent_preloads';

    /**
     * Dynamic Intersections
     */
    private const CONFIG_PATH_SPECULATION_RULES_DYNAMIC_INTERSECTIONS_ENABLED =
        'basecom_performance/speculation_rules_toolbox/dynamic_intersections/enabled';
    private const CONFIG_PATH_SPECULATION_RULES_DYNAMIC_INTERSECTIONS_PRELOAD_TYPE =
        'basecom_performance/speculation_rules_toolbox/dynamic_intersections/preload_type';
    private const CONFIG_PATH_SPECULATION_RULES_DYNAMIC_INTERSECTIONS_CONCURRENT_PRELOADS =
        'basecom_performance/speculation_rules_toolbox/dynamic_intersections/concurrent_preloads';
    private const CONFIG_PATH_SPECULATION_RULES_DYNAMIC_INTERSECTIONS_OBSERVER_DELAY =
        'basecom_performance/speculation_rules_toolbox/dynamic_intersections/observer_delay';

    private const CONFIG_PATH_SPECULATION_RULES_DYNAMIC_INTERSECTIONS_OBSERVER_THRESHOLD =
        'basecom_performance/speculation_rules_toolbox/dynamic_intersections/observer_threshold';
    private const CONFIG_PATH_SPECULATION_RULES_DYNAMIC_INTERSECTIONS_APPLY_TO_PAGES =
        'basecom_performance/speculation_rules_toolbox/dynamic_intersections/apply_to_pages';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly StoreManagerInterface $storeManager,
    ) {
    }

    /**
     * Retrieve a config value for a given path based on the current store
     *
     * @param string $path
     * @return string|null
     */
    private function getConfigValue(string $path): ?string
    {
        try {
            $currentStoreId = $this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            $currentStoreId = $this->storeManager->getDefaultStoreView()?->getId();
        }
        /** @phpstan-ignore-next-line getValue() returns mixed, but the config-values are all saved as strings (text) */
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $currentStoreId
        );
    }

    /**
     * Check if speculations rules are enabled. Overrules all other settings.
     *
     * @return bool
     */
    public function isSpeculationRulesEnabled(): bool
    {
        return (bool)$this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_ENABLED);
    }

    /**
     * @return string
     */
    public function getSpeculationRulesBannedTerms(): string
    {
        $bannedTerms = $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_BANNED_TERMS) ?? '';
        return str_replace(' ', '', $bannedTerms);
    }

    /**
     * @return bool
     */
    public function isSpeculationRulesModerateEnabled(): bool
    {
        return (bool)$this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_MODERATE_ENABLED);
    }

    /**
     * @return string
     */
    public function getSpeculationRulesPreloadType(): string
    {
        return $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_PRELOAD_TYPE) ??
            self::DEFAULT_PRELOAD_TYPE;
    }

    /**
     * @return bool
     */
    public function isAllPagesCustomLinksEnabled(): bool
    {
        return (bool)$this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_ALL_PAGES_CUSTOM_LINKS_ENABLED);
    }

    /**
     * @return string
     */
    public function getAllPagesPreloadTypeCustom(): string
    {
        return $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_ALL_PAGES_PRELOAD_TYPE_CUSTOM) ??
            self::DEFAULT_PRELOAD_TYPE;
    }

    /**
     * @return array
     */
    public function getAllPagesCustomLinks(): array
    {
        $customLinks = $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_ALL_PAGES_CUSTOM_LINKS) ?? '';
        return $this->processCustomLinks($customLinks);
    }

    /**
     * @return bool
     */
    public function isAllPagesCustomScriptEnabled(): bool
    {
        return (bool)$this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_ALL_PAGES_CUSTOM_SCRIPT_ENABLED);
    }

    /**
     * @return string
     */
    public function getAllPagesCustomScript(): string
    {
        $customScript = trim($this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_ALL_PAGES_CUSTOM_SCRIPT) ?? '');
        $customScript = $this->jsonHeal($customScript);

        // Validate the script is correct JSON, to avoid JS-Errors
        if (!$this->jsonValidate($customScript)) {
            return '';
        }

        return $this->removeEscapeSequences($customScript);
    }

    /**
     * @return bool
     */
    public function isHomepageCustomLinksEnabled(): bool
    {
        return (bool)$this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_HOMEPAGE_CUSTOM_LINKS_ENABLED);
    }

    /**
     * @return string
     */
    public function getHomepagePreloadTypeCustom(): string
    {
        return $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_HOMEPAGE_PRELOAD_TYPE_CUSTOM) ??
            self::DEFAULT_PRELOAD_TYPE;
    }

    /**
     * @return array
     */
    public function getHomepageCustomLinks(): array
    {
        $customLinks = $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_HOMEPAGE_CUSTOM_LINKS) ?? '';
        return $this->processCustomLinks($customLinks);
    }

    /**
     * @return bool
     */
    public function isHomepageDynamicTargetsEnabled(): bool
    {
        return (bool)$this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_HOMEPAGE_DYNAMIC_TARGETS_ENABLED);
    }

    /**
     * @return string
     */
    public function getHomepagePreloadTypeDynamic(): string
    {
        return $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_HOMEPAGE_PRELOAD_TYPE_DYNAMIC) ??
            self::DEFAULT_PRELOAD_TYPE;
    }

    /**
     * @return string
     */
    public function getHomePageCssSelector(): string
    {
        return $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_HOMEPAGE_CSS_SELECTOR) ?? '';
    }

    /**
     * @return array
     */
    public function getHomepageConcurrentPreloads(): array
    {
        $concurrentPreloads = $this->getConfigValue(
            self::CONFIG_PATH_SPECULATION_RULES_HOMEPAGE_CONCURRENT_PRELOADS
        ) ?? self::DEFAULT_CONCURRENT_PRELOADS;
        return $this->formatConcurrentPreloadsAsArray($concurrentPreloads);
    }

    /**
     * @return bool
     */
    public function isPlpCustomLinksEnabled(): bool
    {
        return (bool)$this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_PLP_CUSTOM_LINKS_ENABLED);
    }

    /**
     * @return string
     */
    public function getPlpPreloadTypeCustom(): string
    {
        return $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_PLP_PRELOAD_TYPE_CUSTOM) ??
            self::DEFAULT_PRELOAD_TYPE;
    }

    /**
     * @return array
     */
    public function getPlpCustomLinks(): array
    {
        $customLinks = $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_PLP_CUSTOM_LINKS) ?? '';
        return $this->processCustomLinks($customLinks);
    }

    /**
     * @return bool
     */
    public function isPlpDynamicTargetsEnabled(): bool
    {
        return (bool)$this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_PLP_DYNAMIC_TARGETS_ENABLED);
    }

    /**
     * @return string
     */
    public function getPlpPreloadTypeDynamic(): string
    {
        return $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_PLP_PRELOAD_TYPE_DYNAMIC) ??
            self::DEFAULT_PRELOAD_TYPE;
    }

    /**
     * @return string
     */
    public function getPlpCssSelector(): string
    {
        return $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_PLP_CSS_SELECTOR) ?? '';
    }

    /**
     * @return array
     */
    public function getPlpConcurrentPreloads(): array
    {
        $concurrentPreloads = $this->getConfigValue(
            self::CONFIG_PATH_SPECULATION_RULES_PLP_CONCURRENT_PRELOADS
        ) ?? self::DEFAULT_CONCURRENT_PRELOADS;
        return $this->formatConcurrentPreloadsAsArray($concurrentPreloads);
    }

    /**
     * @return bool
     */
    public function isPdpCustomLinksEnabled(): bool
    {
        return (bool)$this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_PDP_CUSTOM_LINKS_ENABLED);
    }

    /**
     * @return string
     */
    public function getPdpPreloadTypeCustom(): string
    {
        return $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_PDP_PRELOAD_TYPE_CUSTOM) ??
            self::DEFAULT_PRELOAD_TYPE;
    }

    /**
     * @return array
     */
    public function getPdpCustomLinks(): array
    {
        $customLinks = $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_PDP_CUSTOM_LINKS) ?? '';
        return $this->processCustomLinks($customLinks);
    }

    /**
     * @return bool
     */
    public function isPdpDynamicTargetsEnabled(): bool
    {
        return (bool)$this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_PDP_DYNAMIC_TARGETS_ENABLED);
    }

    /**
     * @return string
     */
    public function getPdpPreloadTypeDynamic(): string
    {
        return $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_PDP_PRELOAD_TYPE_DYNAMIC) ??
            self::DEFAULT_PRELOAD_TYPE;
    }

    /**
     * @return string
     */
    public function getPdpCssSelector(): string
    {
        return $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_PDP_CSS_SELECTOR) ?? '';
    }

    /**
     * @return array
     */
    public function getPdpConcurrentPreloads(): array
    {
        $concurrentPreloads = $this->getConfigValue(
            self::CONFIG_PATH_SPECULATION_RULES_PDP_CONCURRENT_PRELOADS
        ) ?? self::DEFAULT_CONCURRENT_PRELOADS;
        return $this->formatConcurrentPreloadsAsArray($concurrentPreloads);
    }

    /**
     * @return bool
     */
    public function isDynamicIntersectionsEnabled(): bool
    {
        return (bool)$this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_DYNAMIC_INTERSECTIONS_ENABLED);
    }

    /**
     * @return string
     */
    public function getDynamicIntersectionsPreloadType(): string
    {
        return $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_DYNAMIC_INTERSECTIONS_PRELOAD_TYPE) ??
            self::DEFAULT_PRELOAD_TYPE;
    }

    /**
     * @return int[]
     */
    public function getDynamicIntersectionsConcurrentPreloads(): array
    {
        $concurrentPreloads = $this->getConfigValue(
            self::CONFIG_PATH_SPECULATION_RULES_DYNAMIC_INTERSECTIONS_CONCURRENT_PRELOADS
        ) ?? self::DEFAULT_CONCURRENT_PRELOADS;
        return $this->formatConcurrentPreloadsAsArray($concurrentPreloads);
    }

    /**
     * @return int
     */
    public function getDynamicIntersectionsObserverDelay(): int
    {
        return (int) ($this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_DYNAMIC_INTERSECTIONS_OBSERVER_DELAY) ??
            self::DEFAULT_OBSERVER_DELAY);
    }

    /**
     * @return float
     */
    public function getDynamicIntersectionsObserverThreshold(): float
    {
        $threshold = $this->getConfigValue(
            self::CONFIG_PATH_SPECULATION_RULES_DYNAMIC_INTERSECTIONS_OBSERVER_THRESHOLD
        ) ?? self::DEFAULT_OBSERVER_THRESHOLD;
        // Threshold needs to be within range of 0.0 and 1.0
        return max(0.0, min(100.0, (int) $threshold)) / 100.0;
    }

    /**
     * @return string
     */
    public function getDynamicIntersectionsApplyToPages(): string
    {
        return $this->getConfigValue(self::CONFIG_PATH_SPECULATION_RULES_DYNAMIC_INTERSECTIONS_APPLY_TO_PAGES)
            ?? ApplicationScopes::NONE;
    }

    /**
     * Extract the correct value for mobile & desktop concurrent preloads and return them in an array
     *
     * @param string $concurrentPreloads
     * @return array
     */
    private function formatConcurrentPreloadsAsArray(string $concurrentPreloads) : array
    {
        $concurrentPreloads = explode(',', $concurrentPreloads);

        $concurrentPreloadsDesktop = isset($concurrentPreloads[0]) && is_numeric($concurrentPreloads[0]) ?
            (int) $concurrentPreloads[0] : 0;
        $concurrentPreloadsMobile = isset($concurrentPreloads[1]) && is_numeric($concurrentPreloads[1]) ?
            (int) $concurrentPreloads[1] : 0;

        return [
            'desktop' => $concurrentPreloadsDesktop,
            'mobile' => $concurrentPreloadsMobile,
        ];
    }

    /**
     * Remove escape sequences from string and returns exploded result as array
     *
     * @param string $customLinks
     * @return array
     */
    private function processCustomLinks(string $customLinks) : array
    {
        $customLinksSanitized = $this->removeEscapeSequences($customLinks);
        return explode(';', $customLinksSanitized);
    }

    /**
     * Remove escape sequences, such as carriage return, new line and tab
     *
     * @param string $customLinks
     * @return string
     */
    private function removeEscapeSequences(string $customLinks) : string
    {
        return preg_replace('/[\r\n\t]+/', '', $customLinks) ?? '';
    }

    /**
     * Check if a given string contains valid json. Native function in PHP 8.3, should be refactored.
     *
     * @param string $string
     * @return bool
     */
    private function jsonValidate(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Correct user input if curly opening braces were not provided
     *
     * @param string $jsonString
     * @return string
     */
    private function jsonHeal(string $jsonString): string
    {
        if (!str_starts_with($jsonString, '{')) {
            $jsonString = sprintf('{%s}', $jsonString);
        }
        return $jsonString;
    }
}
// phpcs:enable
