<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;

class CopyGstNumberToOrderAddress implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();

        if (!$quote instanceof Quote || !$order instanceof OrderInterface) {
            return;
        }

        $quoteShippingAddress = $quote->getShippingAddress();
        $orderShippingAddress = $order->getShippingAddress();
        if ($quoteShippingAddress && $orderShippingAddress) {
            $orderShippingAddress->setGstNumber((string)$quoteShippingAddress->getGstNumber());
        }

        $quoteBillingAddress = $quote->getBillingAddress();
        $orderBillingAddress = $order->getBillingAddress();
        if ($quoteBillingAddress && $orderBillingAddress) {
            $orderBillingAddress->setGstNumber((string)$quoteBillingAddress->getGstNumber());
        }
    }
}
