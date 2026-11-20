<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Test\Integration\Customer;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Test\Fixture\Customer as CustomerFixture;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

#[DbIsolation(true)]
class AddressBookTest extends TestCase
{
    private const GST_NUMBER = '27AAPFU0939F1ZV';

    #[DataFixture(CustomerFixture::class, as: 'customer')]
    public function testQuoteAddressGstNumberIsSavedInAddressBook(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $customerId = (int)DataFixtureStorageManager::getStorage()->get('customer')->getId();

        /** @var QuoteAddress $quoteAddress */
        $quoteAddress = $objectManager->create(QuoteAddress::class);
        $quoteAddress->setData([
            'address_type' => 'shipping',
            'firstname' => 'Asha',
            'lastname' => 'Patel',
            'street' => '123 Marine Drive',
            'city' => 'Mumbai',
            'region' => 'Maharashtra',
            'postcode' => '400001',
            'country_id' => 'IN',
            'telephone' => '9876543210',
            'gst_number' => self::GST_NUMBER,
        ]);

        $customerAddress = $quoteAddress->exportCustomerAddress();
        $customerAddress->setCustomerId($customerId);
        $this->assertSame(self::GST_NUMBER, $customerAddress->getCustomAttribute('gst_number')?->getValue());

        $addressRepository = $objectManager->get(AddressRepositoryInterface::class);
        $savedAddress = $addressRepository->save($customerAddress);

        $reloadedAddress = $objectManager->create(AddressRepositoryInterface::class)->getById((int)$savedAddress->getId());
        $this->assertSame(self::GST_NUMBER, $reloadedAddress->getCustomAttribute('gst_number')?->getValue());
    }

    #[DataFixture(CustomerFixture::class, ['addresses' => [[]]], as: 'customer')]
    public function testSavedGstNumberIsPrefilledInQuoteAddress(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $customerId = (int)DataFixtureStorageManager::getStorage()->get('customer')->getId();
        $customer = $objectManager->get(CustomerRepositoryInterface::class)->getById($customerId);
        $addresses = $customer->getAddresses();
        $this->assertNotEmpty($addresses, 'Customer fixture has no addresses');

        $customerAddress = reset($addresses);
        $customerAddress->setCustomAttribute('gst_number', self::GST_NUMBER);
        $objectManager->get(AddressRepositoryInterface::class)->save($customerAddress);

        $reloadedAddress = $objectManager->create(AddressRepositoryInterface::class)->getById((int)$customerAddress->getId());

        /** @var QuoteAddress $quoteAddress */
        $quoteAddress = $objectManager->create(QuoteAddress::class);
        $quoteAddress->importCustomerAddressData($reloadedAddress);

        $this->assertSame(self::GST_NUMBER, $quoteAddress->getData('gst_number'));
    }
}
