<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Observer;

use LokiCheckout\IndianGst\Component\Checkout\Address\GstNumber\GstNumberViewModel;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ClearGstNumberOnCountryChange implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        $event = $observer->getEvent();
        if ($event->getFieldName() !== 'country_id' || $event->getValue() === GstNumberViewModel::COUNTRY_ID) {
            return;
        }

        $address = $event->getAddress();
        if ($address instanceof DataObject && $address->getData('gst_number') !== null) {
            $address->setData('gst_number', null);
        }
    }
}
