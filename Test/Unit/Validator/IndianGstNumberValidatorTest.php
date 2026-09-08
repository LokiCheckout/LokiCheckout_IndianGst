<?php
declare(strict_types=1);

namespace LokiCheckout\IndianGst\Test\Unit\Validator;

use LokiCheckout\IndianGst\Validator\IndianGstNumberValidator;
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
            ['27AAAPL1234C1Z5'],
            ['22AAAAA0000A1Z5'],
            ['29AABCB1234D1Z9'],
            ['06BACHJ9910L1Z5'],
            ['33AADCS1234M1Z3'],
        ];
    }

    public static function invalidGstNumbers(): array
    {
        return [
            ['too short'],
            ['27AAAPL1234C1Z'],
            ['27aaapl1234c1z5'],
            ['27AAAPL1234C1Y5'],
            ['27AABCB1234D1Z9extra'],
            ['123456789012345'],
            ['AAAAAAAAAAAAAAA'],
            ['GSTIN27AAAPL1234C1Z5'],
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

    /**
     * @dataProvider validGstNumbers
     */
    public function testValidGstNumbersPass(string $value): void
    {
        $result = $this->validator->validate($value);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider invalidGstNumbers
     */
    public function testInvalidGstNumbersFail(string $value): void
    {
        $result = $this->validator->validate($value);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertStringContainsString('Invalid GST Number format', $result[0]);
    }

    /**
     * @dataProvider emptyValues
     */
    public function testEmptyValuesPass(mixed $value): void
    {
        $result = $this->validator->validate($value);
        $this->assertTrue($result);
    }
}
