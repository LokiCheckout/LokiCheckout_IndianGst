<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Test\Integration\Component\Checkout\Address\GstNumber;

use LokiCheckout\Core\Test\Integration\LokiCheckoutTestCase;
use LokiCheckout\IndianGst\Test\Fixture\CartAddressFixture;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Quote\Test\Fixture\AddProductToCart as AddProductToCartFixture;
use Magento\Quote\Test\Fixture\GuestCart as GuestCartFixture;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\Config as ConfigFixture;
use Magento\TestFramework\Fixture\DataFixture;

#[AppArea('frontend')]
class GstNumberPageTest extends LokiCheckoutTestCase
{
    private const SHIPPING = 'loki-checkout.shipping.address.gst_number';
    private const BILLING = 'loki-checkout.billing.address.gst_number';
    private const VALID_GST = '27AAPFU0939F1ZV';

    #[DataFixture(ProductFixture::class, as: 'product')]
    #[DataFixture(GuestCartFixture::class, as: 'cart')]
    #[DataFixture(AddProductToCartFixture::class, ['cart_id' => '$cart.id$', 'product_id' => '$product.id$'])]
    #[DataFixture(CartAddressFixture::class, ['cart_id' => '$cart.id$', 'gst_number' => self::VALID_GST])]
    #[DataFixture(CartAddressFixture::class, ['cart_id' => '$cart.id$', 'address_type' => 'billing', 'country_id' => 'NL'])]
    public function testFieldIsOnlyRenderedForIndianShippingAddress(): void
    {
        $this->dispatchToCheckout();

        $shippingComponent = $this->getComponent(self::SHIPPING);
        $this->assertTrue($shippingComponent->getViewModel()->isAllowRendering());
        $this->assertFalse($shippingComponent->getViewModel()->isRequired());
        $this->assertSame(self::VALID_GST, $shippingComponent->getRepository()->getValue());
        $this->assertFalse($this->getComponent(self::BILLING)->getViewModel()->isAllowRendering());

        $this->assertComponentsOnPage([self::SHIPPING], [self::BILLING]);
        $this->assertStringOccursOnPage('placeholder="22AAAAA0000A1ZC"');
    }

    #[DataFixture(ProductFixture::class, as: 'product')]
    #[DataFixture(GuestCartFixture::class, as: 'cart')]
    #[DataFixture(AddProductToCartFixture::class, ['cart_id' => '$cart.id$', 'product_id' => '$product.id$'])]
    #[DataFixture(CartAddressFixture::class, ['cart_id' => '$cart.id$', 'country_id' => 'NL'])]
    #[DataFixture(CartAddressFixture::class, ['cart_id' => '$cart.id$', 'address_type' => 'billing'])]
    public function testFieldIsOnlyRenderedForIndianBillingAddress(): void
    {
        $this->dispatchToCheckout();

        $this->assertFalse($this->getComponent(self::SHIPPING)->getViewModel()->isAllowRendering());
        $this->assertTrue($this->getComponent(self::BILLING)->getViewModel()->isAllowRendering());
        $this->assertComponentsOnPage([], [self::SHIPPING]);
    }

    #[DataFixture(ProductFixture::class, as: 'product')]
    #[DataFixture(GuestCartFixture::class, as: 'cart')]
    #[DataFixture(AddProductToCartFixture::class, ['cart_id' => '$cart.id$', 'product_id' => '$product.id$'])]
    #[DataFixture(CartAddressFixture::class, ['cart_id' => '$cart.id$', 'country_id' => 'NL'])]
    #[DataFixture(CartAddressFixture::class, ['cart_id' => '$cart.id$', 'address_type' => 'billing', 'country_id' => 'NL'])]
    public function testFieldIsNotRenderedOutsideIndia(): void
    {
        $this->dispatchToCheckout();

        $this->assertComponentsOnPage([], [self::SHIPPING, self::BILLING]);
    }

    #[DataFixture(ProductFixture::class, as: 'product')]
    #[DataFixture(GuestCartFixture::class, as: 'cart')]
    #[DataFixture(AddProductToCartFixture::class, ['cart_id' => '$cart.id$', 'product_id' => '$product.id$'])]
    #[DataFixture(CartAddressFixture::class, ['cart_id' => '$cart.id$'])]
    #[DataFixture(CartAddressFixture::class, ['cart_id' => '$cart.id$', 'address_type' => 'billing', 'country_id' => 'NL'])]
    #[ConfigFixture('loki_checkout/indian_gst/required', 1, 'store', 'default')]
    public function testFieldIsRequiredWhenConfigured(): void
    {
        $this->dispatchToCheckout();

        $shippingComponent = $this->getComponent(self::SHIPPING);
        $this->assertTrue($shippingComponent->getViewModel()->isRequired());
        $this->assertContains('required', $shippingComponent->getValidators());
        $this->assertFalse($this->getComponent(self::BILLING)->getViewModel()->isRequired());

        $this->assertComponentValidationCases($shippingComponent, [
            ['', false, ['Value is required']],
            [self::VALID_GST, true, []],
        ]);
    }

    #[DataFixture(ProductFixture::class, as: 'product')]
    #[DataFixture(GuestCartFixture::class, as: 'cart')]
    #[DataFixture(AddProductToCartFixture::class, ['cart_id' => '$cart.id$', 'product_id' => '$product.id$'])]
    #[DataFixture(CartAddressFixture::class, ['cart_id' => '$cart.id$'])]
    public function testServerSideValidation(): void
    {
        $this->dispatchToCheckout();

        $component = $this->getComponent(self::SHIPPING);
        $this->assertContains('indianGstNumber', $component->getValidators());
        $this->assertNotContains('required', $component->getValidators());

        $this->assertComponentValidationCases($component, [
            ['', true, []],
            [self::VALID_GST, true, []],
            ['27AAPFU0939F1Z', false, ['Invalid GST Number format. Expected format: 22AAAAA0000A1ZC']],
            ['39AAPFU0939F1ZV', false, ['Invalid GST Number: The first two digits are not a valid GST state code']],
            ['27AAPFU0939F1Z5', false, ['Invalid GST Number: The check character does not match']],
        ]);
    }

    #[DataFixture(ProductFixture::class, as: 'product')]
    #[DataFixture(GuestCartFixture::class, as: 'cart')]
    #[DataFixture(AddProductToCartFixture::class, ['cart_id' => '$cart.id$', 'product_id' => '$product.id$'])]
    #[DataFixture(CartAddressFixture::class, ['cart_id' => '$cart.id$'])]
    public function testValueIsSavedInUppercaseWithoutSpaces(): void
    {
        $this->dispatchToCheckout();

        $component = $this->getComponent(self::SHIPPING);
        $filteredValue = $this->getFilter()->filter($component, '27 aapfu 0939f1zv');
        $this->assertSame(self::VALID_GST, $filteredValue);

        $component->getRepository()->saveValue($filteredValue);
        $this->assertSame(self::VALID_GST, $this->getQuote()->getShippingAddress()->getGstNumber());
    }

    public function testCountryComponentsTargetGstNumber(): void
    {
        $this->assertContains(
            self::SHIPPING,
            $this->getComponent('loki-checkout.shipping.address.country_id')->getViewModel()->getTargets()
        );
        $this->assertContains(
            self::BILLING,
            $this->getComponent('loki-checkout.billing.address.country_id')->getViewModel()->getTargets()
        );
    }
}
