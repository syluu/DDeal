<?php
/**
 * Mage-World
 *
 *  @category    Mage-World
 *  @package     MW
 *  @author      Mage-world Developer
 *
 *  @copyright   Copyright (c) 2018 Mage-World (https://www.mage-world.com/)
 */

namespace MW\DailyDeal\Model\System\Config;

class Layout implements \Magento\Framework\Data\OptionSourceInterface
{
    const ONE_COLUMN = 1;
    const TWO_COLUMN_RIGHT = 2;
    const TWO_COLUMN_LEFT = 3;
    const EMPTY_COLUMN = 0;
    const THREE_COLUMNS = 4;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = array();
        foreach (self::getOptionHash() as $value => $label) {
            $options[] = array(
                'value'    => $value,
                'label'    => $label
            );
        }
        return $options;
    }

    /**
     * get available statuses.
     *
     * @return []
     */
    public static function getOptionHash()
    {
        return [
            self::ONE_COLUMN => __('1 Column')
            , self::TWO_COLUMN_RIGHT => __('2 Columns Right Sidebar')
            , self::TWO_COLUMN_LEFT => __('2 Columns Left Sidebar')
            , self::THREE_COLUMNS => __('3 Columns')
            , self::EMPTY_COLUMN => __('Empty')
        ];
    }
}
