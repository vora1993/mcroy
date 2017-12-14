<?php

namespace Application\Entity;

interface CreditCardInterface
{
    public function getId();
    public function getName();
    public function getLogo();
    public function getDataAttributes();
    public function getColor();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
    public function getBankId();
}