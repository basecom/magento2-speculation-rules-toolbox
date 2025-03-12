<?php
declare(strict_types=1);
namespace Basecom\SpeculationRulesToolbox\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ApplicationScopes implements OptionSourceInterface
{
    public const NONE = 'no_pages';
    public const ALL_PAGES = 'all_pages';
    public const HOMEPAGE = 'homepage';
    public const PLP = 'plp';
    public const PDP = 'pdp';

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::ALL_PAGES, 'label' => __('All pages')],
            ['value' => self::HOMEPAGE, 'label' => __('Homepage')],
            ['value' => self::PLP, 'label' => __('Product Listing Page (PLP)')],
            ['value' => self::PDP, 'label' => __('Product Detail Page (PDP)')]
        ];
    }
}
