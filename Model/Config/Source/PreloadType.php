<?php
declare(strict_types=1);
namespace Basecom\SpeculationRulesToolbox\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class PreloadType implements OptionSourceInterface
{
    public const PRELOAD_TYPE_PREFETCH = 'prefetch';
    public const PRELOAD_TYPE_PRERENDER = 'prerender';

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::PRELOAD_TYPE_PREFETCH, 'label' => __('Prefetch')],
            ['value' => self::PRELOAD_TYPE_PRERENDER, 'label' => __('Prerender')]
        ];
    }
}
