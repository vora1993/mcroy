<?php

namespace Application\Entity;

interface MenuInterface
{
    public function getId();
    public function getGroupId();
    public function getTitle();
    public function getParent();
    public function getName();
    public function getRoute();
    public function getAction();
    public function getValue();
    public function getSortOrder();
    public function getDateAdded();
    public function getDateModified();
}