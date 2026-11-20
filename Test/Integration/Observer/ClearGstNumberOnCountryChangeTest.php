<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Test\Integration\Observer;

use Magento\Framework\Event\ManagerInterface;
use Magento\Quote\Model\Quote\Address;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

#[AppArea('frontend')]
class ClearGstNumberOnCountryChangeTest extends TestCase
{
    public function testGstNumberIsClearedWhenCountryIsNotIndia(): void
    {
        $address = $this->dispatch('country_id', 'NL');
        $this->assertNull($address->getData('gst_number'));
    }

    public function testGstNumberIsKeptWhenCountryIsIndia(): void
    {
        $address = $this->dispatch('country_id', 'IN');
        $this->assertSame('27AAPFU0939F1ZV', $address->getData('gst_number'));
    }

    public function testGstNumberIsKeptWhenOtherFieldChanges(): void
    {
        $address = $this->dispatch('city', 'Amsterdam');
        $this->assertSame('27AAPFU0939F1ZV', $address->getData('gst_number'));
    }

    private function dispatch(string $fieldName, string $value): Address
    {
        $address = Bootstrap::getObjectManager()->create(Address::class);
        $address->setData(['country_id' => $value, 'gst_number' => '27AAPFU0939F1ZV']);

        Bootstrap::getObjectManager()->get(ManagerInterface::class)->dispatch(
            'loki_checkout_address_field_set_after',
            [
                'field_name' => $fieldName,
                'value' => $value,
                'address' => $address,
                'address_type' => 'shipping',
            ]
        );

        return $address;
    }
}
