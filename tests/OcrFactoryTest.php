<?php

namespace byrokrat\billing;

/**
 * @covers byrokrat\billing\OcrFactory
 */
class OcrFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateOcr()
    {
        $this->assertEquals(
            '12345682',
            (string)(new OcrFactory)->createOcr('123456')
        );
    }

    public function testExceptionOnInvalidLength()
    {
        $this->setExpectedException('byrokrat\billing\RuntimeException');
        (new OcrFactory)->createOcr('123456789012345678901234');
    }

    public function testExceptionOnNonNumericValue()
    {
        $this->setExpectedException('byrokrat\billing\RuntimeException');
        (new OcrFactory)->createOcr('123L');
    }
}