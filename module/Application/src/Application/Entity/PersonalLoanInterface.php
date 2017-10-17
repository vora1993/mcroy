<?php

namespace Application\Entity;

interface PersonalLoanInterface
{
    public function getId();
    public function getUserId();
    public function getLoanId();
    public function getType();
    public function getCategoryId();
    public function getName();
    public function getEmail();
    public function getPhone();
    public function getCompanyName();
    public function getRemark();
    public function getIntRate();
    public function getLoanAmount();
    public function getLoanTenure();
    public function getMonthlyPayment();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}