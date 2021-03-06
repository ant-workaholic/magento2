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
namespace Magento\Checkout\Helper;

use \Magento\Framework\App\Action\Action;
use \Magento\Framework\Object;

class CartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $cartMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $checkoutSessionMock;

    /**
     * @var Cart
     */
    protected $helper;

    protected function setUp()
    {
        $this->urlBuilderMock = $this->getMock('\Magento\Framework\UrlInterface');
        $this->requestMock = $this->getMock(
            '\Magento\Framework\App\RequestInterface',
            array(
                'getRouteName',
                'getControllerName',
                'getParam',
                'setActionName',
                'getActionName',
                'setModuleName',
                'getModuleName',
            )
        );
        $contextMock = $this->getMock('\Magento\Framework\App\Helper\Context', [], [], '', false);
        $contextMock->expects($this->any())->method('getUrlBuilder')->will($this->returnValue($this->urlBuilderMock));
        $contextMock->expects($this->any())->method('getRequest')->will($this->returnValue($this->requestMock));
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->coreHelperMock = $this->getMock('\Magento\Core\Helper\Data', [], [], '', false);
        $this->scopeConfigMock = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->cartMock = $this->getMock('\Magento\Checkout\Model\Cart', [], [], '', false);
        $this->checkoutSessionMock = $this->getMock('\Magento\Checkout\Model\Session', [], [], '', false);

        $this->helper = new Cart(
            $contextMock,
            $this->storeManagerMock,
            $this->coreHelperMock,
            $this->scopeConfigMock,
            $this->cartMock,
            $this->checkoutSessionMock
        );

    }

    public function testGetCart()
    {
        $this->assertEquals($this->cartMock, $this->helper->getCart());
    }

    public function testGetRemoveUrl()
    {
        $quoteItemId = 1;
        $quoteItemMock = $this->getMock('\Magento\Sales\Model\Quote\Item', [], [], '', false);
        $quoteItemMock->expects($this->any())->method('getId')->will($this->returnValue($quoteItemId));
        $currentUrl = 'http://www.example.com/';
        $this->urlBuilderMock->expects($this->any())->method('getCurrentUrl')->will($this->returnValue($currentUrl));
        $params = array(
            'id' => $quoteItemId,
            Action::PARAM_NAME_BASE64_URL => strtr(base64_encode($currentUrl), '+/=', '-_,'),
        );
        $this->urlBuilderMock->expects($this->once())->method('getUrl')->with('checkout/cart/delete', $params);
        $this->helper->getRemoveUrl($quoteItemMock);
    }

    public function testGetCartUrl()
    {
        $this->urlBuilderMock->expects($this->once())->method('getUrl')->with('checkout/cart', array());
        $this->helper->getCartUrl();
    }

    public function testGetQuote()
    {
        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $this->checkoutSessionMock->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));
        $this->assertEquals($quoteMock, $this->helper->getQuote());
    }

    public function testGetItemsCount()
    {
        $itemsCount = 1;
        $this->cartMock->expects($this->any())->method('getItemsCount')->will($this->returnValue($itemsCount));
        $this->assertEquals($itemsCount, $this->helper->getItemsCount());
    }

    public function testGetItemsQty()
    {
        $itemsQty = 1;
        $this->cartMock->expects($this->any())->method('getItemsQty')->will($this->returnValue($itemsQty));
        $this->assertEquals($itemsQty, $this->helper->getItemsQty());
    }

    public function testGetSummaryCount()
    {
        $summaryQty = 1;
        $this->cartMock->expects($this->any())->method('getSummaryQty')->will($this->returnValue($summaryQty));
        $this->assertEquals($summaryQty, $this->helper->getSummaryCount());
    }

    public function testGetIsVirtualQuote()
    {
        $isVirtual = true;
        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $this->checkoutSessionMock->expects($this->once())->method('getQuote')->will($this->returnValue($quoteMock));
        $quoteMock->expects($this->any())->method('isVirtual')->will($this->returnValue($isVirtual));
        $this->assertEquals($isVirtual, $this->helper->getIsVirtualQuote());
    }

    public function testGetShouldRedirectToCart()
    {
        $storeId = 1;
        $this->scopeConfigMock->expects($this->once())->method('isSetFlag')
            ->with(Cart::XML_PATH_REDIRECT_TO_CART, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)
            ->will($this->returnValue(true));
        $this->assertTrue($this->helper->getShouldRedirectToCart($storeId));
    }

    public function testGetAddUrl()
    {
        $productEntityId = 1;
        $storeId = 1;
        $productMock = $this->getMock('\Magento\Catalog\Model\Product',
            ['getEntityId', 'hasUrlDataObject', 'getUrlDataObject', '__wakeup'], [], '', false);
        $productMock->expects($this->any())->method('getEntityId')->will($this->returnValue($productEntityId));
        $productMock->expects($this->any())->method('hasUrlDataObject')->will($this->returnValue(true));
        $productMock->expects($this->any())->method('getUrlDataObject')
            ->will($this->returnValue(new Object(array('store_id' => $storeId))));

        $currentUrl = 'http://www.example.com/';
        $this->urlBuilderMock->expects($this->any())->method('getCurrentUrl')->will($this->returnValue($currentUrl));
        $this->coreHelperMock->expects($this->any())->method('urlEncode')->with($currentUrl)
            ->will($this->returnValue($currentUrl));

        $this->requestMock->expects($this->any())->method('getRouteName')->will($this->returnValue('checkout'));
        $this->requestMock->expects($this->any())->method('getControllerName')->will($this->returnValue('cart'));

        $params = array(
            Action::PARAM_NAME_URL_ENCODED => $currentUrl,
            'product' => $productEntityId,
            'custom_param' => 'value',
            '_scope' => $storeId,
            '_scope_to_url' => true,
            'in_cart' => 1,
        );

        $this->urlBuilderMock->expects($this->once())->method('getUrl')->with('checkout/cart/add', $params);
        $this->helper->getAddUrl($productMock, array('custom_param' => 'value'));
    }
}
