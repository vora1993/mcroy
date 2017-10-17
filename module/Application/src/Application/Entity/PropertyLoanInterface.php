<?php

namespace Application\Entity;

interface PropertyLoanInterface
{
    public function getId();
    public function getUserId();
    public function getType();
    public function getCategoryId();
    public function getPropertyType();
    public function getProjectName();
    public function getPropertyStatus();
    public function getOptionFee();
    public function getOfferOpts();
    public function getExisting();
    public function getRemark();
    public function getIntRate();
    public function getLoanAmount();
    public function getLoanTenure();
    public function getLoanPercent();
    public function getFixedRates();
    public function getMonthlyPayment();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}