<?php
namespace Application\Entity;

class Media implements MediaInterface
{
	protected $id;
    protected $title;
    protected $src;
    protected $author_id;
    protected $caption;
    protected $alt;
    protected $size;
    protected $type;
    protected $description;
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
    
    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
    public function getSrc()
    {
        return $this->src;
    }
    public function setSrc($src)
    {
        $this->src = $src;
        return $this;
    }
    
    public function getAuthorId()
    {
        return $this->author_id;
    }
    public function setAuthorId($author_id)
    {
        $this->author_id = (int) $author_id;
        return $this;
    }
    
    public function getCaption()
    {
        return $this->caption;
    }
    public function setCaption($caption)
    {
        $this->caption = $caption;
        return $this;
    }
    
    public function getAlt()
    {
        return $this->alt;
    }
    public function setAlt($alt)
    {
        $this->alt = $alt;
        return $this;
    }
    
    public function getSize()
    {
        return $this->size;
    }
    public function setSize($size)
    {
        $this->size = (int) $size;
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
    
    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description)
    {
        $this->description = $description;
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