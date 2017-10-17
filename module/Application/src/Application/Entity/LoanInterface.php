<?php

namespace Application\Entity;

interface LoanInterface
{
    public function getId();
    public function getBankId();
    public function getType();
    public function getCategoryId();
    public function getLoanTitle();
    public function getPromotions();
    public function getBenefit();
    public function getInterestRate();
    public function getMaxLoanAmount();
    public function getMaxTenor();
    public function getProcessingFee();
    public function getAnnualFee();
    public function getPenaltyFee();
    public function getLockInPeriod();
    public function getMinSalesTurnover();
    public function getMinYearsOfIncorporation();
    public function getUrl();
    public function getIntRate();
    public function getMaxTenure();
    public function getMaxLoanAmt();
    public function getPrepaymentPenaltyFee();
    public function getRestructuringOfLoanTenor();
    public function getMinTurnover();
    public function getMinYearsIncorporation();
    public function getMinAge();
    public function getBankruptcy();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}