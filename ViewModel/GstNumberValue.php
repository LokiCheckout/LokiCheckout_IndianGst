<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\ViewModel;

use LokiCheckout\Core\Util\Component\AddressStateProvider;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class GstNumberValue implements ArgumentInterface
{
    public function __construct(
        private AddressStateProvider $addressStateProvider,
    ) {
    }

    public function getValue(AbstractBlock $block): string
    {
        $addressState = $this->addressStateProvider->getAddressState($block);

        return trim((string)$addressState->get('gst_number'), " \f\n\r\t\v\x00");
    }
}
