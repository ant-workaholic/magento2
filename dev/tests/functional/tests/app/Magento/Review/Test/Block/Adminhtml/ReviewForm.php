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

namespace Magento\Review\Test\Block\Adminhtml;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Form;

/**
 * Class Edit
 * Review edit form
 */
class ReviewForm extends Form
{
    /**
     * Posted by field
     *
     * @var string
     */
    protected $customer = '#customer';

    /**
     * Rating status
     *
     * @var string
     */
    protected $status = '[name=status_id]';

    /**
     * 'Save Review' button
     *
     * @var string
     */
    protected $saveButton = '[data-ui-id$=save-button-button]';

    /**
     * Selector for single rating
     *
     * @var string
     */
    protected $ratingByNumber = './/*[@id="detailed_rating"]//*[contains(@class,"field-rating")][%d]';

    /**
     * Selector for label of checked rating
     *
     * @var string
     */
    protected $checkedRating = 'input[id$="_%d"]:checked + label';

    /**
     * Get data from 'Posted By' field
     *
     * @return string
     */
    public function getPostedBy()
    {
        return $this->_rootElement->find($this->customer, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Get data from Status field
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_rootElement->find($this->status, Locator::SELECTOR_CSS, 'select')->getText();
    }

    /**
     * Set approve review
     *
     * @return void
     */
    public function setApproveReview()
    {
        $this->_rootElement->find($this->status, Locator::SELECTOR_CSS, 'select')->setValue('Approved');
    }

    /**
     * Get list ratings
     *
     * @return array
     */
    public function getRatings()
    {
        $ratings = [];

        $count = 1;
        $rating = $this->_rootElement->find(sprintf($this->ratingByNumber, $count), Locator::SELECTOR_XPATH);
        while ($rating->isVisible()) {
            $ratings[$count] = [
                'title' => $rating->find('./label/span', Locator::SELECTOR_XPATH)->getText(),
                'rating' => $this->getRatingVote($rating)
            ];

            ++$count;
            $rating = $this->_rootElement->find(sprintf($this->ratingByNumber, $count), Locator::SELECTOR_XPATH);
        }

        return $ratings;
    }

    /**
     * Get rating vote
     *
     * @param Element $rating
     * @return int
     */
    protected function getRatingVote(Element $rating)
    {
        $ratingVote = 5;
        $ratingVoteElement = $rating->find(sprintf($this->checkedRating, $ratingVote));
        while (!$ratingVoteElement->isVisible() && $ratingVote) {
            --$ratingVote;
            $ratingVoteElement = $rating->find(sprintf($this->checkedRating, $ratingVote));
        }

        return $ratingVote;
    }
}
