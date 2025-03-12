<?php
declare(strict_types=1);
namespace Basecom\SpeculationRulesToolbox\Api\Data;

// Disable PHPCS, as forced short descriptions on getters are redundant
// phpcs:disable
interface ConfigInterface
{
    /**
     * @return bool
     */
    public function isSpeculationRulesEnabled(): bool;

    /**
     * @return string
     */
    public function getSpeculationRulesBannedTerms(): string;

    /**
     * @return bool
     */
    public function isSpeculationRulesModerateEnabled(): bool;

    /**
     * @return string
     */
    public function getSpeculationRulesPreloadType(): string;

    /**
     * @return bool
     */
    public function isAllPagesCustomLinksEnabled(): bool;

    /**
     * @return string
     */
    public function getAllPagesPreloadTypeCustom(): string;

    /**
     * @return array
     */
    public function getAllPagesCustomLinks(): array;

    /**
     * @return bool
     */
    public function isAllPagesCustomScriptEnabled(): bool;

    /**
     * @return string
     */
    public function getAllPagesCustomScript(): string;

    /**
     * @return bool
     */
    public function isHomepageCustomLinksEnabled(): bool;

    /**
     * @return string
     */
    public function getHomepagePreloadTypeCustom(): string;

    /**
     * @return array
     */
    public function getHomepageCustomLinks(): array;

    /**
     * @return bool
     */
    public function isHomepageDynamicTargetsEnabled(): bool;

    /**
     * @return string
     */
    public function getHomepagePreloadTypeDynamic(): string;

    /**
     * @return string
     */
    public function getHomePageCssSelector(): string;

    /**
     * @return array
     */
    public function getHomepageConcurrentPreloads(): array;

    /**
     * @return bool
     */
    public function isPlpCustomLinksEnabled(): bool;

    /**
     * @return string
     */
    public function getPlpPreloadTypeCustom(): string;

    /**
     * @return array
     */
    public function getPlpCustomLinks(): array;

    /**
     * @return bool
     */
    public function isPlpDynamicTargetsEnabled(): bool;

    /**
     * @return string
     */
    public function getPlpPreloadTypeDynamic(): string;

    /**
     * @return string
     */
    public function getPlpCssSelector(): string;

    /**
     * @return array
     */
    public function getPlpConcurrentPreloads(): array;

    /**
     * @return bool
     */
    public function isPdpCustomLinksEnabled(): bool;

    /**
     * @return string
     */
    public function getPdpPreloadTypeCustom(): string;

    /**
     * @return array
     */
    public function getPdpCustomLinks(): array;

    /**
     * @return bool
     */
    public function isPdpDynamicTargetsEnabled(): bool;

    /**
     * @return string
     */
    public function getPdpPreloadTypeDynamic(): string;

    /**
     * @return string
     */
    public function getPdpCssSelector(): string;

    /**
     * @return array
     */
    public function getPdpConcurrentPreloads(): array;

    /**
     * @return bool
     */
    public function isDynamicIntersectionsEnabled(): bool;
    /**
     * @return string
     */
    public function getDynamicIntersectionsPreloadType(): string;

    /**
     * @return int[]
     */
    public function getDynamicIntersectionsConcurrentPreloads(): array;

    /**
     * @return int
     */
    public function getDynamicIntersectionsObserverDelay(): int;

    /**
     * @return float
     */
    public function getDynamicIntersectionsObserverThreshold(): float;

    /**
     * @return string
     */
    public function getDynamicIntersectionsApplyToPages(): string;
}
// phpcs:enable
