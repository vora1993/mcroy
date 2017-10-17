<?php
namespace Application\Entity;

interface ReferralInterface
{
    public function getId();
    public function getUserId();
    public function getType();
    public function getApplication();
    public function getCredit();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}
