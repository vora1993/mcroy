BusinessLoanEligibility.php
<?php
namespace Application\Entity;

class BusinessLoanEligibility implements BusinessLoanEligibilityInterface
{
  protected $id;
  protected $user_id;
  protected $data;
  protected $date_added;
  protected $date_modified;

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