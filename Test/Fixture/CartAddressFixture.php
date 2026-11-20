<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Test\Fixture;

use Magento\Framework\DataObject;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Fixture\DataFixtureInterface;

class CartAddressFixture implements DataFixtureInterface
{
    private const DEFAULT_DATA = [
        'firstname' => 'Asha',
        'lastname' => 'Patel',
        'country_id' => 'IN',
        'region_id' => 0,
        'region' => 'Maharashtra',
        'city' => 'Mumbai',
        'postcode' => '400001',
        'street' => ['123 Marine Drive'],
        'telephone' => '9876543210',
    ];

    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
    ) {
    }

    public function apply(array $data = []): ?DataObject
    {
        /** @var Quote $cart */
        $cart = $this->cartRepository->get($data['cart_id']);
        $addressType = $data['address_type'] ?? 'shipping';
        unset($data['cart_id'], $data['address_type']);

        $address = $addressType === 'billing' ? $cart->getBillingAddress() : $cart->getShippingAddress();
        $address->addData(array_merge(self::DEFAULT_DATA, $data));
        $cart->getShippingAddress()->setSameAsBilling(false);

        if ($addressType === 'shipping') {
            $address->setShippingMethod('flatrate_flatrate');
        }

        $this->cartRepository->save($cart);

        return $cart;
    }
}
