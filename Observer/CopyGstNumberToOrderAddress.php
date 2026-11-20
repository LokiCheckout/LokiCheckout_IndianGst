<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Observer;

use LokiCheckout\IndianGst\Component\Checkout\Address\GstNumber\GstNumberViewModel;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

class CopyGstNumberToOrderAddress implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();

        if (!$quote instanceof Quote || !$order instanceof Order) {
            return;
        }

        $this->copy($quote->getShippingAddress(), $order->getShippingAddress());
        $this->copy($quote->getBillingAddress(), $order->getBillingAddress());
    }

    private function copy(?DataObject $quoteAddress, ?DataObject $orderAddress): void
    {
        if ($quoteAddress === null || $orderAddress === null) {
            return;
        }

        $gstNumber = (string)$quoteAddress->getData('gst_number');
        if ($gstNumber === '' || $orderAddress->getData('country_id') !== GstNumberViewModel::COUNTRY_ID) {
            $orderAddress->setData('gst_number', null);
            return;
        }

        $orderAddress->setData('gst_number', $gstNumber);
    }
}
