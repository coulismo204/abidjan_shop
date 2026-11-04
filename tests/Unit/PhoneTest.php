<?php

namespace Tests\Unit;

use App\Support\Phone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{
    public static function numbers(): array
    {
        return [
            ['07 08 09 10 11', '0708091011'],
            ['+225 05.85.94.58.07', '0585945807'],
            ['00225 0102030405', '0102030405'],
            ['2250708091011', '0708091011'],
            ['27 22 00 00 00', '2722000000'],
            ['0808091011', null],   // préfixe inexistant
            ['070809101', null],    // 9 chiffres
            ['', null],
        ];
    }

    #[DataProvider('numbers')]
    public function test_normalize(string $input, ?string $expected): void
    {
        $this->assertSame($expected, Phone::normalize($input));
    }
}
