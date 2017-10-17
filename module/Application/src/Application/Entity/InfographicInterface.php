<?php

namespace Application\Entity;

interface InfographicInterface
{
    public function getId();
    public function getTitle();
    public function getImage();
    public function getPdf();
    public function getCreatedBy();
    public function getModifiedBy();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}