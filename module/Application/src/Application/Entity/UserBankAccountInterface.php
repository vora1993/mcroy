<?php

namespace Application\Entity;

interface UserBankAccountInterface
{
    public function getId();
    public function getName();
    public function getUserId();
    public function getBank();
    public function getAcctNo();
    public function getDateAdded();
    public function getDateModified();
}