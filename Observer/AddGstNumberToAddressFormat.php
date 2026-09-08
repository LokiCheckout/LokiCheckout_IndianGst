<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddGstNumberToAddressFormat implements ObserverInterface
{
    private const HTML_SUFFIX = '{{depend gst_number}}<br/>GST: {{var gst_number}}{{/depend}}';
    private const TEXT_SUFFIX = "\n{{depend gst_number}}GST: {{var gst_number}}{{/depend}}";

    public function execute(Observer $observer): void
    {
        $type = $observer->getEvent()->getType();
        if ($type === null) {
            return;
        }

        $format = (string)$type->getDefaultFormat();
        if (str_contains($format, 'gst_number')) {
            return;
        }

        $typeCode = (string)$type->getCode();
        $suffix = $typeCode === 'html' ? self::HTML_SUFFIX : self::TEXT_SUFFIX;
        $type->setDefaultFormat($format . $suffix);
    }
}
