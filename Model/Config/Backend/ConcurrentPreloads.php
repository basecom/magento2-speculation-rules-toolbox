<?php
declare(strict_types=1);

namespace Basecom\SpeculationRulesToolbox\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

/**
 * Validate the concurrent preloads field in the admin area
 */
class ConcurrentPreloads extends Value
{
    /**
     * Ensure that the value provided follows the expected format. Whitespaces will be stripped.
     *
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave() : self
    {
        $concurrentPreloadsValue = $this->getValue();
        $concurrentPreloadsConfigPath = $this->getPath();

        if (!preg_match('/^\d+\s*,\s*\d+$/', $concurrentPreloadsValue)) {
            throw new LocalizedException(__(
                'Invalid format for config "<b>%1</b>". Use "<b>number,number</b>" (e.g., <b>10,3</b>)',
                $concurrentPreloadsConfigPath
            ));
        }

        $this->setValue(str_replace(' ', '', $concurrentPreloadsValue));

        return parent::beforeSave();
    }
}
