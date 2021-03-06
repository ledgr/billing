<?php

declare(strict_types=1);

namespace byrokrat\billing;

use byrokrat\amount\Amount;

class InvoiceBuilderTest extends TestCase
{
    private $builder;

    protected function setup()
    {
        $this->builder = (new InvoiceBuilder)
            ->setSerial('1')
            ->setSeller($this->createMock(AgentInterface::CLASS))
            ->setBuyer($this->createMock(AgentInterface::CLASS));
    }

    public function testExceptionWhenSerialNotSet()
    {
        $this->expectException(Exception::CLASS);
        (new InvoiceBuilder)->getSerial();
    }

    public function testExceptionWhenSellerNotSet()
    {
        $this->expectException(Exception::CLASS);
        (new InvoiceBuilder)->getSeller();
    }

    public function testExceptionWhenBuyerNotSet()
    {
        $this->expectException(Exception::CLASS);
        (new InvoiceBuilder)->getBuyer();
    }

    public function testBuildInvoice()
    {
        $ocr = '232';
        $item = $this->getBillableMock('', new Amount('0'));
        $date = new \DateTimeImmutable();
        $deduction = new Amount('100');

        $invoice = $this->builder
            ->setOcr($ocr)
            ->addItem($item)
            ->setBillDate($date)
            ->setExpiresAfter(1)
            ->setDeduction($deduction)
            ->buildInvoice();

        $this->assertSame($ocr, $invoice->getOcr());
        $this->assertInstanceOf(ItemBasket::CLASS, $invoice->getItems());
        $this->assertSame($date, $invoice->getBillDate());
        $this->assertSame($deduction, $invoice->getDeduction());
    }

    public function testGnerateWithoutBillDate()
    {
        $this->assertInstanceOf(
            'DateTimeImmutable',
            $this->builder->buildInvoice()->getBillDate()
        );
    }

    public function testGeneratingWithoutOcr()
    {
        $this->assertEmpty(
            $this->builder->buildInvoice()->getOcr()
        );
    }

    public function testGenerateOcr()
    {
        $this->assertNotEmpty(
            $this->builder->generateOcr()->buildInvoice()->getOcr()
        );
    }

    public function testAttributes()
    {
        $this->assertSame(
            'bar',
            $this->builder->setAttribute('foo', 'bar')->buildInvoice()->getAttribute('foo')
        );
    }
}
