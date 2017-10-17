<?php

namespace Application\Entity;

interface MenuGroupInterface
{
    public function getId();
    public function getName();
    public function getSortOrder();
    public function getIsDefault();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}