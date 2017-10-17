<?php
namespace Application\Entity;

interface UserRefInterface
{
    public function getId();
    public function getUserId();
    public function getRef();
    public function getDateAdded();
}
