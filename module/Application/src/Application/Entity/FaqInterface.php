<?php

namespace Application\Entity;

interface FaqInterface
{
    public function getId();
    public function getType();
    public function getQuestion();
    public function getAnswer();
    public function getCreatedBy();
    public function getModifiedBy();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}