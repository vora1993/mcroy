<?php
namespace Application\Entity;

class Slider implements SliderInterface
{
	protected $id;
    protected $name;
    protected $url;
    protected $sort_order;
    protected $date_added;
    protected $date_modified;
    protected $status;
    protected $type;
    protected $link;
    protected $style;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }
    public function setUrl($url)
    {
        $this->url = $url;
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

    public function getSortOrder()
    {
        return $this->sort_order;
    }
    public function setSortOrder($sort_order)
    {
        $this->sort_order = (int) $sort_order;
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

    public function getType()
    {
        return $this->type;
    }
    public function setType($type)
    {
        $this->type = (int) $type;
        return $this;
    }

    public function getLink()
    {
        return $this->link;
    }
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    public function getStyle()
    {
        return $this->style;
    }
    public function setStyle($style)
    {
        $this->style = (int)$style;
        return $this;
    }
}