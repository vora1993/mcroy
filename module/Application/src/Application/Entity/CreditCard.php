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
  protected $cashback;
  protected $discount;
  protected $points;
  protected $air_miles;
  protected $bank_id;
  protected $apply_url;
  protected $provider_ids;


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

  public function getProviderIds()
  {
    return $this->provider_ids;
  }
  public function setProviderIds($provider_ids)
  {
    $this->provider_ids = $provider_ids;
    return $this;
  }

  public function getCashback()
  {
    return $this->cashback;
  }

  public function setCashback($cashback)
  {
    $this->cashback = $cashback;
    return $this;
  }

  public function getDiscount()
  {
    return $this->discount;
  }

  public function setDiscount($discount)
  {
    $this->discount = $discount;
    return $this;
  }

  public function getPoints()
  {
    return $this->points;
  }

  public function setPoints($points)
  {
    $this->points = $points;
    return $this;
  }

  public function getAirMiles()
  {
    return $this->air_miles;
  }

  public function setAirMiles($air_miles)
  {
    $this->air_miles = $air_miles;
    return $this;
  }

  public function getApplyUrl()
  {
    return $this->apply_url;
  }

  public function setApplyUrl($apply_url)
  {
    $this->apply_url = $apply_url;
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