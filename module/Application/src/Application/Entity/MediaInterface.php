<?php

namespace Application\Entity;

interface MediaInterface
{
    public function getId();
    public function getTitle();
    public function getSrc();
    public function getAuthorId();
    public function getCaption();
    public function getAlt();
    public function getType();
    public function getSize();
    public function getDescription();
    public function getDateAdded();
    public function getDateModified();
}