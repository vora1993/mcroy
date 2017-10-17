<?php

namespace Application\Entity;

interface WidgetInterface
{
    public function getId();
    public function getType();
    public function getName();
    public function getContent();
    public function getLink();
    public function getLabelLink();
    public function getCreatedBy();
    public function getModifiedBy();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}