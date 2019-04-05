<?php

namespace Application\Entity;

interface BankAccountPackageInterface
{
    public function getId();
    public function getBankId();
    public function getCategoryId();
    public function getCategoryAccount();
    public function getLoanTitle();
    public function getPromotions();
    public function getLink();
    public function getInitialDepositAmount();
    public function getInterestRate();
    public function getMinimumBalance();
    public function getChequeBookFees();
    public function getInternetBankingFees();
    public function getAnnualFee();
    public function getServiceFee();
    public function getHighlight();
    public function getIntRate();
    public function getCitizenship();
    public function getAge();
    public function getTenor();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}