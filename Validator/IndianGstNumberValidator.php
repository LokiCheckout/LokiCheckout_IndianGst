<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Validator\ValidatorInterface;

class IndianGstNumberValidator implements ValidatorInterface
{
    private const GSTIN_PATTERN = '/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/';
    private const CHARACTERS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const EXTRA_STATE_CODES = [96, 97, 99];
    private const MAX_STATE_CODE = 38;

    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        $gstNumber = strtoupper((string)preg_replace('/\s+/', '', (string)$value));

        if ($gstNumber === '') {
            return true;
        }

        if (!preg_match(self::GSTIN_PATTERN, $gstNumber)) {
            return [(string)__('Invalid GST Number format. Expected format: 22AAAAA0000A1ZC')];
        }

        if (!$this->hasValidStateCode($gstNumber)) {
            return [(string)__('Invalid GST Number: The first two digits are not a valid GST state code')];
        }

        if ($this->getCheckCharacter($gstNumber) !== $gstNumber[14]) {
            return [(string)__('Invalid GST Number: The check character does not match')];
        }

        return true;
    }

    private function hasValidStateCode(string $gstNumber): bool
    {
        $stateCode = (int)substr($gstNumber, 0, 2);
        if ($stateCode >= 1 && $stateCode <= self::MAX_STATE_CODE) {
            return true;
        }

        return in_array($stateCode, self::EXTRA_STATE_CODES, true);
    }

    private function getCheckCharacter(string $gstNumber): string
    {
        $sum = 0;
        for ($position = 0; $position < 14; $position++) {
            $product = (int)strpos(self::CHARACTERS, $gstNumber[$position]) * (($position % 2) + 1);
            $sum += intdiv($product, 36) + ($product % 36);
        }

        return self::CHARACTERS[(36 - ($sum % 36)) % 36];
    }
}
