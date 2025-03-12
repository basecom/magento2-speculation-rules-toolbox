<?php
declare(strict_types=1);

namespace Basecom\SpeculationRulesToolbox\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

/**
 * Validate the observer threshold field in the admin area
 */
class ObserverThreshold extends Value
{
    /**
     * Ensure that the value provided is between 0 and 100
     *
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave() : self
    {
        $concurrentPreloadsValue = (int) $this->getValue();
        $concurrentPreloadsConfigPath = $this->getPath();

        if ($concurrentPreloadsValue < 0 || $concurrentPreloadsValue > 100) {
            throw new LocalizedException(__(
                'Invalid format for config "<b>%1</b>". The value needs to be between <b>0</b> and <b>100</b>',
                $concurrentPreloadsConfigPath
            ));
        }

        return parent::beforeSave();
    }
}
