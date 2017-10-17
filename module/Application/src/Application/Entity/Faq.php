<?php
namespace Application\Entity;

class Faq implements FaqInterface
{
	protected $id;
    protected $type;
    protected $answer;
    protected $question;
    protected $created_by;
    protected $modified_by;
    protected $date_added;
    protected $date_modified;
    protected $status;
    
    
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }
    
    public function getType()
    {
        return $this->type;
    }
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    
    public function getQuestion()
    {
        return $this->question;
    }
    public function setQuestion($question)
    {
        $this->question = $question;
        return $this;
    }
    
    public function getAnswer()
    {
        return $this->answer;
    }
    public function setAnswer($answer)
    {
        $this->answer = $answer;
        return $this;
    }
    
    public function getCreatedBy()
    {
        return $this->created_by;
    }
    public function setCreatedBy($created_by)
    {
        $this->created_by = (int) $created_by;
        return $this;
    }
    
    public function getModifiedBy()
    {
        return $this->modified_by;
    }
    public function setModifiedBy($modified_by)
    {
        $this->modified_by = (int) $modified_by;
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