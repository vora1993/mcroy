<?php

namespace Application\Entity;

interface BankInterface
{
    public function getId();
    public function getName();
    public function getLogo();
    public function getColor();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
    public function getLogoInCreditCard();
    public function getShowCreditCard();
}