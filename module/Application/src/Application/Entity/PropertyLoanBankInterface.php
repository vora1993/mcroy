<?php

namespace Application\Entity;

interface PropertyLoanBankInterface
{
    public function getId();
    public function getTitle();
    public function getBankId();
    public function getPromotions();
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
    public function getIntYear2();
    public function getIntYear3();
    public function getIntYear4();
    public function getRemark();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}