<?php

declare(strict_types=1);

namespace byrokrat\billing;

use byrokrat\amount\Amount;

/**
 * Basic billable implementation
 */
class SimpleItem implements Billable
{
    /**
     * @var string
     */
    private $description;

    /**
     * @var Amount
     */
    private $unitCost;

    /**
     * @var int
     */
    private $units;

    /**
     * @var Amount
     */
    private $vat;

    /**
     * Set immutable data at construct
     *
     * Note that a VAT of 25% is represented as .25
     */
    public function __construct(string $description, Amount $unitCost, int $units = 1, Amount $vat = null)
    {
        $this->description = $description;
        $this->unitCost = $unitCost;
        $this->units = $units;
        $this->vat = $vat ?: new Amount('.25');
    }

    public function getBillingDescription(): string
    {
        return $this->description;
    }

    public function getCostPerUnit(): Amount
    {
        return $this->unitCost;
    }

    public function getNrOfUnits(): int
    {
        return $this->units;
    }

    public function getVatRate(): Amount
    {
        return $this->vat;
    }
}
