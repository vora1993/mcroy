<?php
namespace Application\Entity;

interface UserCreditInterface
{
    public function getId();
    public function getUserId();
    public function getRefId();
    public function getCredit();
    public function getDateAdded();
}
