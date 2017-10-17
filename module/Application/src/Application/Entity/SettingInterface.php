<?php

namespace Application\Entity;

interface SettingInterface
{
    public function getId();
    public function getName();
    public function getKey();
    public function getValue();
    public function getRemark();
    public function getDateAdded();
    public function getDateModified();
}