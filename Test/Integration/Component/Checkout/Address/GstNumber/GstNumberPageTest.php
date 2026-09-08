<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Test\Integration\Component\Checkout\Address\GstNumber;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class GstNumberPageTest extends TestCase
{
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
    }

    public function testComponentXmlIsValid(): void
    {
        $componentXml = file_get_contents(
            __DIR__ . '/../../../../etc/loki_components.xml'
        );

        $this->assertStringContainsString('loki-checkout.shipping.address.gst_number', $componentXml);
        $this->assertStringContainsString('loki-checkout.billing.address.gst_number', $componentXml);
        $this->assertStringContainsString('indianGstNumber', $componentXml);
        $this->assertStringContainsString('shippingAddressFields', $componentXml);
        $this->assertStringContainsString('billingAddressFields', $componentXml);
    }

    public function testCountryHandleXmlIsValid(): void
    {
        $layoutXml = file_get_contents(
            __DIR__ . '/../../../../view/frontend/layout/loki_checkout_country_in.xml'
        );

        $this->assertStringContainsString('loki-checkout.shipping.address.gst_number', $layoutXml);
        $this->assertStringContainsString('loki-checkout.billing.address.gst_number', $layoutXml);
        $this->assertStringContainsString('gst_number', $layoutXml);
    }

    public function testValidatorIsRegisteredInDi(): void
    {
        $diXml = file_get_contents(
            __DIR__ . '/../../../../etc/di.xml'
        );

        $this->assertStringContainsString('indianGstNumber', $diXml);
        $this->assertStringContainsString('IndianGstNumberValidator', $diXml);
    }

    public function testFieldsetXmlIsValid(): void
    {
        $fieldsetXml = file_get_contents(
            __DIR__ . '/../../../../etc/fieldset.xml'
        );

        $this->assertStringContainsString('sales_convert_quote_address', $fieldsetXml);
        $this->assertStringContainsString('gst_number', $fieldsetXml);
        $this->assertStringContainsString('to_order_address', $fieldsetXml);
        $this->assertStringContainsString('to_customer_address', $fieldsetXml);
        $this->assertStringContainsString('to_quote_address', $fieldsetXml);
    }
}
