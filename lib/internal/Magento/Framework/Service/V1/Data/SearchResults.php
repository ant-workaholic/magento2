<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Framework\Service\V1\Data;

/**
 * SearchResults Service Data Object used for the search service requests
 */
class SearchResults extends \Magento\Framework\Service\Data\AbstractObject
{
    const KEY_ITEMS = 'items';
    const KEY_SEARCH_CRITERIA = 'search_criteria';
    const KEY_TOTAL_COUNT = 'total_count';

    /**
     * Get items
     *
     * @return \Magento\Framework\Service\Data\AbstractObject[]
     */
    public function getItems()
    {
        return is_null($this->_get(self::KEY_ITEMS)) ? [] : $this->_get(self::KEY_ITEMS);
    }

    /**
     * Get search criteria
     *
     * @return \Magento\Framework\Service\V1\Data\SearchCriteria
     */
    public function getSearchCriteria()
    {
        return $this->_get(self::KEY_SEARCH_CRITERIA);
    }

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->_get(self::KEY_TOTAL_COUNT);
    }
}
