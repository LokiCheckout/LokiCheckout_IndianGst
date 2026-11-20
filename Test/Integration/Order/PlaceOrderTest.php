<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Test\Integration\Order;

use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Checkout\Test\Fixture\SetBillingAddress as SetBillingAddressFixture;
use Magento\Checkout\Test\Fixture\SetDeliveryMethod as SetDeliveryMethodFixture;
use Magento\Checkout\Test\Fixture\SetGuestEmail as SetGuestEmailFixture;
use Magento\Checkout\Test\Fixture\SetPaymentMethod as SetPaymentMethodFixture;
use Magento\Checkout\Test\Fixture\SetShippingAddress as SetShippingAddressFixture;
use Magento\Directory\Model\RegionFactory;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Quote\Test\Fixture\AddProductToCart as AddProductToCartFixture;
use Magento\Quote\Test\Fixture\GuestCart as GuestCartFixture;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

#[
    DbIsolation(true),
    AppArea('frontend'),
    DataFixture(ProductFixture::class, as: 'product'),
    DataFixture(GuestCartFixture::class, as: 'cart'),
    DataFixture(SetGuestEmailFixture::class, ['cart_id' => '$cart.id$']),
    DataFixture(AddProductToCartFixture::class, ['cart_id' => '$cart.id$', 'product_id' => '$product.id$']),
    DataFixture(SetBillingAddressFixture::class, ['cart_id' => '$cart.id$']),
    DataFixture(SetShippingAddressFixture::class, ['cart_id' => '$cart.id$']),
    DataFixture(SetDeliveryMethodFixture::class, ['cart_id' => '$cart.id$']),
    DataFixture(SetPaymentMethodFixture::class, ['cart_id' => '$cart.id$', 'method' => ['method' => 'checkmo']]),
]
class PlaceOrderTest extends TestCase
{
    public function testGstNumbersAreStoredOnOrderAddresses(): void
    {
        $order = $this->placeOrder(
            ['country_id' => 'IN', 'gst_number' => '27AAPFU0939F1ZV'],
            ['country_id' => 'IN', 'gst_number' => '07AAGFF2194N1Z1'],
        );

        $this->assertSame('27AAPFU0939F1ZV', $order->getShippingAddress()->getData('gst_number'));
        $this->assertSame('07AAGFF2194N1Z1', $order->getBillingAddress()->getData('gst_number'));
    }

    public function testEmptyGstNumbersAreStoredAsNull(): void
    {
        $order = $this->placeOrder(
            ['country_id' => 'IN', 'gst_number' => ''],
            ['country_id' => 'IN', 'gst_number' => null],
        );

        $this->assertNull($order->getShippingAddress()->getData('gst_number'));
        $this->assertNull($order->getBillingAddress()->getData('gst_number'));
    }

    public function testGstNumberIsNotStoredOutsideIndia(): void
    {
        $order = $this->placeOrder(
            ['country_id' => 'IN', 'gst_number' => '27AAPFU0939F1ZV'],
            ['country_id' => 'US', 'gst_number' => '07AAGFF2194N1Z1'],
        );

        $this->assertSame('27AAPFU0939F1ZV', $order->getShippingAddress()->getData('gst_number'));
        $this->assertNull($order->getBillingAddress()->getData('gst_number'));
    }

    private function placeOrder(array $shippingData, array $billingData): Order
    {
        $objectManager = Bootstrap::getObjectManager();
        $cartId = (int)DataFixtureStorageManager::getStorage()->get('cart')->getId();
        $cartRepository = $objectManager->get(CartRepositoryInterface::class);

        /** @var Quote $quote */
        $quote = $cartRepository->get($cartId);
        $this->applyAddressData($quote->getShippingAddress(), $shippingData);
        $this->applyAddressData($quote->getBillingAddress(), $billingData);
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $cartRepository->save($quote);

        $orderId = (int)$objectManager->get(CartManagementInterface::class)->placeOrder($cartId);

        /** @var Order $order */
        $order = $objectManager->create(OrderRepositoryInterface::class)->get($orderId);
        $this->assertInstanceOf(Order\Address::class, $order->getShippingAddress());
        $this->assertInstanceOf(Order\Address::class, $order->getBillingAddress());

        return $order;
    }

    private function applyAddressData(QuoteAddress $address, array $data): void
    {
        if ($data['country_id'] === 'IN') {
            $region = Bootstrap::getObjectManager()->get(RegionFactory::class)->create()->loadByCode('MH', 'IN');
            $data += [
                'region_id' => $region->getId() ? (int)$region->getId() : null,
                'region' => 'Maharashtra',
                'city' => 'Mumbai',
                'postcode' => '400001',
            ];
        }

        $address->addData($data);
    }
}
