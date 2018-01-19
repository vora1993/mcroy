<?php

namespace Application\Entity;

interface SliderInterface
{
    public function getId();
    public function getName();
    public function getUrl();
    public function getSortOrder();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
    public function getType();
    public function getLink();
}