<?php
namespace Application\Entity;

class CreditCard implements CreditCardInterface
{
  protected $id;
  protected $name;
  protected $logo;
  protected $color;
  protected $date_added;
  protected $date_modified;
  protected $status;
  protected $data_attributes;
  protected $bank_id;


  public function getId()
  {
    return $this->id;
  }
  public function setId($id)
  {
    $this->id = (int) $id;
    return $this;
  }

  public function getName()
  {
    return $this->name;
  }
  public function setName($name)
  {
    $this->name = $name;
    return $this;
  }

  public function getLogo()
  {
    return $this->logo;
  }
  public function setLogo($logo)
  {
    $this->logo = $logo;
    return $this;
  }

  public function getDataAttributes()
  {
    return $this->data_attributes;
  }
  public function setDataAttributes($data_attributes)
  {
    $this->data_attributes = $data_attributes;
    return $this;
  }

  public function getBankId()
  {
    return $this->bank_id;
  }
  public function setBankId($bank_id)
  {
    $this->bank_id = $bank_id;
    return $this;
  }

  public function getColor()
  {
    return $this->color;
  }

  public function setColor($color)
  {
    $this->color = $color;
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

  public function getStatus()
  {
    return $this->status;
  }
  public function setStatus($status)
  {
    $this->status = (int) $status;
    return $this;
  }
}