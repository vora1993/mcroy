<?php

namespace Application\Entity;

interface RoleInterface
{
    public function getId();
    public function getName();
    public function getKey();
    public function getChild();
    public function getIsDefault();
    public function getAllow();
    public function getDeny();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}