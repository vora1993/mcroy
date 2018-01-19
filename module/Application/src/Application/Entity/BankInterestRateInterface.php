<?php

namespace Application\Entity;

interface BankInterestRateInterface
{
    public function getId();
    public function getBankId();
    public function getName();
    public function getRate();
}