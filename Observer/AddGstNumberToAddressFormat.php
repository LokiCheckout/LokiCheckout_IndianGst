<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddGstNumberToAddressFormat implements ObserverInterface
{
    private const SUFFIXES = [
        'html' => '{{depend gst_number}}<br/>GST: {{var gst_number}}{{/depend}}',
        'pdf' => '{{depend gst_number}}GST: {{var gst_number}}|{{/depend}}',
        'oneline' => '{{depend gst_number}}, GST: {{var gst_number}}{{/depend}}',
    ];

    private const DEFAULT_SUFFIX = "\n{{depend gst_number}}GST: {{var gst_number}}{{/depend}}";

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
        $suffix = self::SUFFIXES[$typeCode] ?? self::DEFAULT_SUFFIX;
        $type->setDefaultFormat($format . $suffix);
    }
}
