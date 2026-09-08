<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Test\Integration\Setup\Patch\Data;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Eav\Model\Config as EavConfig;
use PHPUnit\Framework\TestCase;

class AddGstNumberFieldToCustomerAddressTest extends TestCase
{
    private EavConfig $eavConfig;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->eavConfig = $objectManager->get(EavConfig::class);
    }

    public function testGstNumberAttributeExists(): void
    {
        $attribute = $this->eavConfig->getAttribute('customer_address', 'gst_number');
        $this->assertNotNull($attribute->getId());
        $this->assertSame('GST Number', $attribute->getFrontendLabel());
    }

    public function testGstNumberAttributeIsUserDefined(): void
    {
        $attribute = $this->eavConfig->getAttribute('customer_address', 'gst_number');
        $this->assertTrue((bool)$attribute->getIsUserDefined());
    }

    public function testGstNumberAttributeIsNotRequired(): void
    {
        $attribute = $this->eavConfig->getAttribute('customer_address', 'gst_number');
        $this->assertFalse((bool)$attribute->getIsRequired());
    }

    public function testGstNumberAttributeUsedInForms(): void
    {
        $attribute = $this->eavConfig->getAttribute('customer_address', 'gst_number');
        $usedInForms = $attribute->getUsedInForms();

        $this->assertContains('adminhtml_customer', $usedInForms);
        $this->assertContains('adminhtml_customer_address', $usedInForms);
        $this->assertContains('adminhtml_checkout', $usedInForms);
        $this->assertContains('customer_address_edit', $usedInForms);
    }
}
