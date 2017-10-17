<?php

namespace Application\Entity;

interface PropertyLoanPackageInterface
{
    public function getId();
    public function getTitle();
    public function getBankId();
    public function getCategoryId();
    public function getPromotions();
    public function getMinLoanAmount();
    public function getProperty();
    public function getType();
    public function getPropertyStatus();
    public function getPackage();
    public function getFloatingType();
    public function getSibor();
    public function getVariable();
    public function getSor();
    public function getLockInYear();
    public function getIntYear1();
    public function getRemarkYear1();
    public function getIntYear2();
    public function getRemarkYear2();
    public function getIntYear3();
    public function getRemarkYear3();
    public function getIntYear4();
    public function getRemarkYear4();
    public function getLegalSubsidy();
    public function getLegalFeeSubsidy();
    public function getValuationSubsidy();
    public function getFireInsuranceSubsidy();
    public function getSubsidyComment();
    public function getClawback();
    public function getValuationFee();
    public function getLatePaymentFee();
    public function getEarlyRepaymentFee();
    public function getCancellationFee();
    public function getPreferredFire();
    public function getAdminFee();
    public function getRemark();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}