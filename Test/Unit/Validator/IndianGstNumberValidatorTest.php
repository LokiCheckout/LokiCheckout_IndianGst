<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Test\Unit\Validator;

use LokiCheckout\IndianGst\Validator\IndianGstNumberValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IndianGstNumberValidatorTest extends TestCase
{
    private IndianGstNumberValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new IndianGstNumberValidator();
    }

    public static function validGstNumbers(): array
    {
        return [
            ['27AAPFU0939F1ZV'],
            ['07AAGFF2194N1Z1'],
            ['22AAAAA0000A1ZC'],
            ['27AAAPL1234C1ZE'],
            ['29AABCB1234D1Z8'],
            ['33AADCS1234M1ZH'],
            ['06BACHJ9910L1Z0'],
            ['27aapfu0939f1zv'],
            ['27 AAPFU 0939F1ZV'],
            [" 27AAPFU0939F1ZV\t"],
        ];
    }

    public static function invalidFormats(): array
    {
        return [
            ['too short'],
            ['27AAPFU0939F1Z'],
            ['27AAPFU0939F1YV'],
            ['27AAPFU0939F0ZV'],
            ['27AAPFU0939F1ZVextra'],
            ['123456789012345'],
            ['AAAAAAAAAAAAAAA'],
            ['GSTIN27AAPFU0939F1ZV'],
            ['27AAPFU0939F1Z-'],
        ];
    }

    public static function invalidStateCodes(): array
    {
        return [
            ['00AAPFU0939F1ZV'],
            ['39AAPFU0939F1ZV'],
            ['50AAPFU0939F1ZV'],
            ['98AAPFU0939F1ZV'],
        ];
    }

    public static function invalidCheckCharacters(): array
    {
        return [
            ['27AAPFU0939F1Z5'],
            ['22AAAAA0000A1Z5'],
            ['07AAGFF2194N1Z2'],
            ['29AABCB1234D1Z9'],
        ];
    }

    public static function emptyValues(): array
    {
        return [
            [''],
            [' '],
            [null],
        ];
    }

    #[DataProvider('validGstNumbers')]
    public function testValidGstNumbersPass(string $value): void
    {
        $this->assertTrue($this->validator->validate($value));
    }

    #[DataProvider('invalidFormats')]
    public function testInvalidFormatsFail(string $value): void
    {
        $this->assertSingleError('Invalid GST Number format', $this->validator->validate($value));
    }

    #[DataProvider('invalidStateCodes')]
    public function testInvalidStateCodesFail(string $value): void
    {
        $this->assertSingleError('not a valid GST state code', $this->validator->validate($value));
    }

    #[DataProvider('invalidCheckCharacters')]
    public function testInvalidCheckCharactersFail(string $value): void
    {
        $this->assertSingleError('check character does not match', $this->validator->validate($value));
    }

    #[DataProvider('emptyValues')]
    public function testEmptyValuesPass(mixed $value): void
    {
        $this->assertTrue($this->validator->validate($value));
    }

    private function assertSingleError(string $expectedMessage, bool|array $result): void
    {
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertStringContainsString($expectedMessage, $result[0]);
    }
}
