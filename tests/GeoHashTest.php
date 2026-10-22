<?php

namespace Tests\Fyfey\GeoHash;

use Fyfey\GeoHash\GeoHash;
use PHPUnit\Framework\TestCase;

class GeoHashTest extends TestCase
{
    private GeoHash $geoHash;

    protected function setUp(): void
    {
        $this->geoHash = new GeoHash();
    }

    public function testDecode(): void
    {
        $this->assertSame([42.6, -5.6], $this->geoHash->decode('ezs42', 2));
    }

    public function testDecodeAccurately(): void
    {
        $this->assertSame([42.60498046875, -5.60302734375], $this->geoHash->decode('ezs42'));
    }

    public function testEncode(): void
    {
        $this->assertSame('ezs42', $this->geoHash->encode(42.6, -5.6));
    }

    public function testEncodeWithPrecision(): void
    {
        $this->assertSame('ezs42e44yx96', $this->geoHash->encode(42.6, -5.6, 12));
    }
}
