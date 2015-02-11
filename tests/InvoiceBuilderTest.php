<?php

namespace byrokrat\billing;

use byrokrat\amount\Amount;
use DateTime;

class InvoiceBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $builder;

    protected function setup()
    {
        $this->builder = (new InvoiceBuilder)
            ->setSerial('1')
            ->setSeller(new LegalPerson('seller'))
            ->setBuyer(new LegalPerson('buyer'));
    }

    public function testExceptionWhenSerialNotSet()
    {
        $this->setExpectedException('byrokrat\billing\RuntimeException');
        (new InvoiceBuilder)->getSerial();
    }

    public function testExceptionWhenSellerNotSet()
    {
        $this->setExpectedException('byrokrat\billing\RuntimeException');
        (new InvoiceBuilder)->getSeller();
    }

    public function testExceptionWhenBuyerNotSet()
    {
        $this->setExpectedException('byrokrat\billing\RuntimeException');
        (new InvoiceBuilder)->getBuyer();
    }

    public function testBuildInvoice()
    {
        $ocr = new Ocr('232');
        $post = new InvoicePost('', new Amount('0'), new Amount('0'));
        $date = new DateTime();
        $deduction = new Amount('100');

        $invoice = $this->builder
            ->setMessage('message')
            ->setOcr($ocr)
            ->addPost($post)
            ->setBillDate($date)
            ->setExpiresAfter(1)
            ->setDeduction($deduction)
            ->setCurrency('EUR')
            ->buildInvoice();

        $this->assertSame('message', $invoice->getMessage());
        $this->assertSame($ocr, $invoice->getOcr());
        $this->assertSame([$post], $invoice->getPosts());
        $this->assertSame($date, $invoice->getBillDate());
        $this->assertSame($deduction, $invoice->getDeduction());
        $this->assertSame('EUR', $invoice->getCurrency());
    }

    public function testGnerateWithoutBillDate()
    {
        $this->assertInstanceOf(
            'DateTime',
            $this->builder->buildInvoice()->getBillDate()
        );
    }

    public function testGeneratingWithoutOcr()
    {
        $this->assertNull(
            $this->builder->buildInvoice()->getOcr()
        );
    }

    public function testGenerateOcr()
    {
        $this->assertInstanceOf(
            'byrokrat\billing\Ocr',
            $this->builder->generateOcr()->buildInvoice()->getOcr()
        );
    }
}
