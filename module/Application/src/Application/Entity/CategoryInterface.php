<?php

namespace Application\Entity;

interface CategoryInterface
{
    public function getId();
    public function getName();
    public function getSeo();
    public function getSortOrder();
    public function getDescription();
    public function getType();
    public function getParentId();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
    public function getShowBankInterestRate();
}