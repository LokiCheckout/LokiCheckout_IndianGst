<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Validator\ValidatorInterface;

class IndianGstNumberValidator implements ValidatorInterface
{
    private const GSTIN_PATTERN = '/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/';

    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        $gstNumber = trim((string)$value, " \f\n\r\t\v\x00");

        if ($gstNumber === '') {
            return true;
        }

        if (!preg_match(self::GSTIN_PATTERN, $gstNumber)) {
            return [(string)__('Invalid GST Number format. Expected format: 22AAAAA0000A1Z5')];
        }

        return true;
    }
}
