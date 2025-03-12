<?php
declare(strict_types=1);

namespace Basecom\SpeculationRulesToolbox\ViewModel;

use BadMethodCallException;
use Basecom\SpeculationRulesToolbox\Model\Config as SpeculationRulesConfig;
use Basecom\SpeculationRulesToolbox\Model\Config\Source\ApplicationScopes;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class SpeculationRules implements ArgumentInterface
{
    private const LAYOUT_HANDLE_HOMEPAGE = 'cms_index_index';
    private const LAYOUT_HANDLE_PRODUCT_LISTING_PAGE = 'catalog_category_view';
    private const LAYOUT_HANDLE_SEARCH_RESULTS = 'catalogsearch_result_index';
    private const LAYOUT_HANDLE_PRODUCT_DETAIL_PAGE = 'catalog_product_view';

    /**
     * @param RequestInterface $request
     * @param SpeculationRulesConfig $speculationRulesConfig
     */
    public function __construct(
        /** @var Http $request */
        private readonly RequestInterface $request,
        private readonly SpeculationRulesConfig $speculationRulesConfig
    ) {
    }

    /**
     * If a method cannot be found in this class, call it in the speculation rules config
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->$name(...$arguments);
        }

        if (method_exists($this->speculationRulesConfig, $name)) {
            return $this->speculationRulesConfig->$name(...$arguments);
        }

        throw new BadMethodCallException("Method {$name} does not exist.");
    }

    /**
     * Check whether the current request matches the layout handle for homepage
     *
     * @return bool
     */
    public function isHomepage(): bool
    {
        return $this->request->getFullActionName()
            === self::LAYOUT_HANDLE_HOMEPAGE;
    }

    /**
     *  Check whether the current request matches the layout handle for PLP or Search
     *
     * @return bool
     */
    public function isProductListingPage(): bool
    {
        $fullActionName = $this->request->getFullActionName();

        return in_array($fullActionName, [
            self::LAYOUT_HANDLE_PRODUCT_LISTING_PAGE,
            self::LAYOUT_HANDLE_SEARCH_RESULTS,
        ], true);
    }

    /**
     * Check whether the current request matches the layout handle for PDP
     *
     * @return bool
     */
    public function isProductDetailPage(): bool
    {
        return $this->request->getFullActionName()
            === self::LAYOUT_HANDLE_PRODUCT_DETAIL_PAGE;
    }

    /**
     * Check whether the current request is allowed by the configuration to add the dynamic intersections
     *
     * @return bool
     */
    public function canApplyDynamicRules(): bool
    {
        $applyToPages = $this->speculationRulesConfig->getDynamicIntersectionsApplyToPages();

        // If enabled for all sites, return true. No action name needed.
        if (str_contains($applyToPages, ApplicationScopes::ALL_PAGES)) {
            return true;
        }

        $layoutHandleId = $this->getLayoutHandleId();
        return str_contains($applyToPages, $layoutHandleId);
    }

    /**
     * Maps the layouts handles to the values used in the configuration
     *
     * @return string
     */
    public function getLayoutHandleId() : string
    {
        $fullActionName = $this->request->getFullActionName();

        return match ($fullActionName) {
            self::LAYOUT_HANDLE_HOMEPAGE => ApplicationScopes::HOMEPAGE,
            self::LAYOUT_HANDLE_PRODUCT_DETAIL_PAGE => ApplicationScopes::PDP,
            self::LAYOUT_HANDLE_PRODUCT_LISTING_PAGE,
            self::LAYOUT_HANDLE_SEARCH_RESULTS => ApplicationScopes::PLP,
            default => ApplicationScopes::NONE,
        };
    }
}
