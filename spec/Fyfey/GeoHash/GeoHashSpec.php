<?php

namespace spec\Fyfey\GeoHash;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GeoHashSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Fyfey\GeoHash\GeoHash');
    }

    function it_should_decode_a_hash()
    {
        $hash = "ezs42";

        $this->decode($hash, 2)->shouldBe([42.6, -5.6]);
    }

    function it_should_decode_a_hash_accurately()
    {
        $hash = "ezs42";

        $this->decode($hash)->shouldBe([42.60498046875, -5.60302734375]);
    }

    function it_should_encode_a_hash()
    {
        $this->encode(42.6, -5.6)->shouldBe('ezs42');
    }

    function it_should_encode_a_hash_for_different_precision()
    {
        $this->encode(42.6, -5.6, 12)->shouldBe('ezs42e44yx96');
    }

    public function getMatchers()
    {
        return [
            'beRoughly' => function($subject, $val) {
                for ($i = 0; $i < count($subject); $i++) {
                    if (round($subject[$i], 2) != $val[$i]) {
                        return false;
                    }
                }
                return true;
            }
        ];
    }
}
