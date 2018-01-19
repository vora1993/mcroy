<?php

namespace Application\Entity;

interface BusinessLoanEligibilityInterface
{
  public function getId();
  public function getUserId();
  public function getData();
  public function getToken();
  public function getDateAdded();
  public function getDateModified();
}