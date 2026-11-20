<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Test\Integration\Observer;

use Magento\Sales\Model\Order\Address as OrderAddress;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class AddGstNumberToAddressFormatTest extends TestCase
{
    public function testGstNumberIsRenderedInAllAddressFormats(): void
    {
        $address = $this->createOrderAddress('27AAPFU0939F1ZV');
        $renderer = Bootstrap::getObjectManager()->get(Renderer::class);

        $this->assertStringContainsString('<br/>GST: 27AAPFU0939F1ZV', (string)$renderer->format($address, 'html'));
        $this->assertStringContainsString('GST: 27AAPFU0939F1ZV', (string)$renderer->format($address, 'text'));
        $this->assertStringContainsString('GST: 27AAPFU0939F1ZV|', (string)$renderer->format($address, 'pdf'));
        $this->assertStringContainsString(', GST: 27AAPFU0939F1ZV', (string)$renderer->format($address, 'oneline'));
    }

    public function testNoGstLineWithoutGstNumber(): void
    {
        $address = $this->createOrderAddress(null);
        $renderer = Bootstrap::getObjectManager()->get(Renderer::class);

        foreach (['html', 'text', 'pdf', 'oneline'] as $type) {
            $this->assertStringNotContainsString('GST', (string)$renderer->format($address, $type), $type);
        }
    }

    private function createOrderAddress(?string $gstNumber): OrderAddress
    {
        /** @var OrderAddress $address */
        $address = Bootstrap::getObjectManager()->create(OrderAddress::class);
        $address->setData([
            'address_type' => 'billing',
            'firstname' => 'Asha',
            'lastname' => 'Patel',
            'street' => '123 Marine Drive',
            'city' => 'Mumbai',
            'region' => 'Maharashtra',
            'postcode' => '400001',
            'country_id' => 'IN',
            'telephone' => '9876543210',
            'gst_number' => $gstNumber,
        ]);

        return $address;
    }
}
