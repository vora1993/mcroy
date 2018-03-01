<?php

namespace Application\Entity;

interface PropertyCostOutPlayInterface
{
    public function getId();
    public function getMortgageStampDuty();
    public function getValuationFee();
    public function getLegalFee();
    public function getFireInsurance();
}