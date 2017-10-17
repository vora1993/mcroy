<?php

namespace Application\Entity;

interface SubscribeInterface
{
    public function getId();
    public function getUserId();
    public function getEmail();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}