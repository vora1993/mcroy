<?php

namespace Application\Entity;

interface BankAccountInterface
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
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}