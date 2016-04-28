<?php

declare(strict_types=1);

namespace byrokrat\billing;

use byrokrat\amount\Amount;

/**
 * Create complex invoices
 */
class InvoiceBuilder
{
    /**
     * @var string Invoice serial number
     */
    private $serial;

    /**
     * @var Seller Registered seller
     */
    private $seller;

    /**
     * @var Buyer Registered buyer
     */
    private $buyer;

    /**
     * @var string Message to buyer
     */
    private $message;

    /**
     * @var string Payment reference number
     */
    private $ocr;

    /**
     * @var bool Flag if ocr may be generated from serial
     */
    private $generateOcr;

    /**
     * @var OcrTools Tools for validating and creating ocr numbers
     */
    private $ocrTools;

    /**
     * @var ItemBasket Container of charged items
     */
    private $itemBasket;

    /**
     * @var \DateTime Invoice creation date
     */
    private $billDate;

    /**
     * @var int Number of days before invoice expires
     */
    private $expiresAfter;

    /**
     * @var Amount Prepaid amound to deduct
     */
    private $deduction;

    /**
     * Reset values at construct
     */
    public function __construct(OcrTools $ocrTools = null)
    {
        $this->ocrTools = $ocrTools ?: new OcrTools;
        $this->reset();
    }

    /**
     * Reset builder values
     */
    public function reset(): self
    {
        $this->serial = null;
        $this->seller = null;
        $this->buyer = null;
        $this->message = '';
        $this->ocr = '';
        $this->itemBasket = new ItemBasket;
        $this->generateOcr = false;
        $this->billDate = null;
        $this->expiresAfter = 30;
        $this->deduction = null;
        return $this;
    }

    /**
     * Build invoice
     */
    public function buildInvoice(): Invoice
    {
        return new Invoice(
            $this->getSerial(),
            $this->getSeller(),
            $this->getBuyer(),
            $this->message,
            $this->getOcr(),
            $this->itemBasket,
            $this->billDate ?: new \DateTime,
            $this->expiresAfter,
            $this->deduction
        );
    }

    /**
     * Set invoice serial number
     */
    public function setSerial(string $serial): self
    {
        $this->serial = $serial;
        return $this;
    }

    /**
     * Get invoice serial number
     *
     * @throws Exception If serial is not set
     */
    public function getSerial(): string
    {
        if (isset($this->serial)) {
            return $this->serial;
        }
        throw new Exception("Unable to create invoice: serial not set");
    }

    /**
     * Set seller
     */
    public function setSeller(Seller $seller): self
    {
        $this->seller = $seller;
        return $this;
    }

    /**
     * Get seller
     *
     * @throws Exception If seller is not set
     */
    public function getSeller(): Seller
    {
        if (isset($this->seller)) {
            return $this->seller;
        }
        throw new Exception("Unable to create Invoice: seller not set");
    }

    /**
     * Set buyer
     */
    public function setBuyer(Buyer $buyer): self
    {
        $this->buyer = $buyer;
        return $this;
    }

    /**
     * Get buyer
     *
     * @throws Exception If buyer is not set
     */
    public function getBuyer(): Buyer
    {
        if (isset($this->buyer)) {
            return $this->buyer;
        }
        throw new Exception("Unable to create Invoice: buyer not set");
    }

    /**
     * Set invoice message
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Set invoice reference number
     */
    public function setOcr(string $ocr): self
    {
        $this->ocrTools->validate($ocr);
        $this->ocr = $ocr;
        return $this;
    }

    /**
     * Set if ocr may be generated from serial
     */
    public function generateOcr(bool $generateOcr = true): self
    {
        $this->generateOcr = $generateOcr;
        return $this;
    }

    /**
     * Get invoice reference number
     */
    public function getOcr(): string
    {
        if (!$this->ocr && $this->generateOcr) {
            return $this->ocrTools->create($this->getSerial());
        }

        return $this->ocr;
    }

    /**
     * Add billable to invoice
     */
    public function addItem(Billable $billable): self
    {
        $this->itemBasket->addItem(new ItemEnvelope($billable));
        return $this;
    }

    /**
     * Set date of invoice creation
     */
    public function setBillDate(\DateTime $date): self
    {
        $this->billDate = $date;
        return $this;
    }

    /**
     * Set number of days before invoice expires
     */
    public function setExpiresAfter(int $nrOfDays): self
    {
        $this->expiresAfter = $nrOfDays;
        return $this;
    }

    /**
     * Set deduction (amount prepaid)
     */
    public function setDeduction(Amount $deduction): self
    {
        $this->deduction = $deduction;
        return $this;
    }
}
