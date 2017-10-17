<?php
namespace Application\Entity;

class MediaImport implements MediaImportInterface
{
	protected $id;
    protected $title;
    protected $content;
    protected $time;
    
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
    
    public function getContent()
    {
        return $this->content;
    }
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
    
    public function getTime()
    {
        return $this->time;
    }
    public function setTime($time)
    {
        $this->time = (int) $time;
        return $this;
    }
    
}