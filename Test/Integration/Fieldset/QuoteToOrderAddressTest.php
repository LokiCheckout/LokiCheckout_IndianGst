<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Test\Integration\Fieldset;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\DataObject\Copy as CopyService;
use PHPUnit\Framework\TestCase;

class QuoteToOrderAddressTest extends TestCase
{
    private CopyService $copyService;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->copyService = $objectManager->get(CopyService::class);
    }

    public function testGstNumberIsCopiedFromQuoteAddressToOrderAddress(): void
    {
        $quoteAddress = new \Magento\Framework\DataObject();
        $quoteAddress->setData('gst_number', '27AAAPL1234C1Z5');

        $orderAddressData = $this->copyService->getDataFromFieldset(
            'sales_convert_quote_address',
            'to_order_address',
            $quoteAddress
        );

        $this->assertArrayHasKey('gst_number', $orderAddressData);
        $this->assertSame('27AAAPL1234C1Z5', $orderAddressData['gst_number']);
    }

    public function testGstNumberIsCopiedFromQuoteAddressToCustomerAddress(): void
    {
        $quoteAddress = new \Magento\Framework\DataObject();
        $quoteAddress->setData('gst_number', '29AABCB1234D1Z9');

        $customerAddressData = $this->copyService->getDataFromFieldset(
            'sales_convert_quote_address',
            'to_customer_address',
            $quoteAddress
        );

        $this->assertArrayHasKey('gst_number', $customerAddressData);
        $this->assertSame('29AABCB1234D1Z9', $customerAddressData['gst_number']);
    }

    public function testGstNumberIsCopiedFromCustomerAddressToQuoteAddress(): void
    {
        $customerAddress = new \Magento\Framework\DataObject();
        $customerAddress->setData('gst_number', '06BACHJ9910L1Z5');

        $quoteAddressData = $this->copyService->getDataFromFieldset(
            'customer_address',
            'to_quote_address',
            $customerAddress
        );

        $this->assertArrayHasKey('gst_number', $quoteAddressData);
        $this->assertSame('06BACHJ9910L1Z5', $quoteAddressData['gst_number']);
    }

    public function testEmptyGstNumberCopiesAsNull(): void
    {
        $quoteAddress = new \Magento\Framework\DataObject();
        $quoteAddress->setData('gst_number', null);

        $orderAddressData = $this->copyService->getDataFromFieldset(
            'sales_convert_quote_address',
            'to_order_address',
            $quoteAddress
        );

        $this->assertArrayNotHasKey('gst_number', $orderAddressData);
    }
}
