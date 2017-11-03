<?php
namespace Application\Entity;

class BusinessLoanEligibility implements BusinessLoanEligibilityInterface
{
  protected $id;
  protected $user_id;
  protected $data;
  protected $date_added;
  protected $date_modified;

  public function getId()
  {
    return $this->id;
  }

  public function setId($id)
  {
    $this->id = (int) $id;
    return $this;
  }

  public function getUserId()
  {
    return $this->user_id;
  }

  public function setUserId($id)
  {
    $this->user_id = (int) $user_id;
    return $this;
  }

  public function getData()
  {
    return $this->data;
  }

  public function setData($data)
  {
    $this->data = $data;
    return $this;
  }

  public function getToken()
  {
    return $this->token;
  }

  public function setToken($token)
  {
    $this->token = $token;
    return $this;
  }

  public function getDateAdded()
  {
    return $this->date_added;
  }

  public function setDateAdded($date_added)
  {
    $this->date_added = $date_added;
    return $this;
  }

  public function getDateModified()
  {
    return $this->date_modified;
  }

  public function setDateModified($date_modified)
  {
    $this->date_modified = $date_modified;
    return $this;
  }
}