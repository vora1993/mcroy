<?php
namespace Application\Entity;

class Post implements PostInterface
{
	protected $id;
    protected $post_author;
    protected $post_date;
    protected $post_content;
    protected $category_id;
    protected $post_title;
    protected $post_excerpt;
    protected $seo;
    protected $featured_image;
    protected $hits;
    protected $featured;
    protected $attachment;
    protected $sort_order;
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
    
    public function getPostAuthor()
    {
        return $this->post_author;
    }
    public function setPostAuthor($post_author)
    {
        $this->post_author = (int) $post_author;
        return $this;
    }
    
    public function getPostDate()
    {
        return $this->post_date;
    }
    public function setPostDate($post_date)
    {
        $this->post_date = $post_date;
        return $this;
    }
    
    public function getPostContent()
    {
        return $this->post_content;
    }
    public function setPostContent($post_content)
    {
        $this->post_content = $post_content;
        return $this;
    }
    
    public function getPostTitle()
    {
        return $this->post_title;
    }
    public function setPostTitle($post_title)
    {
        $this->post_title = $post_title;
        return $this;
    }
    
    public function getCategoryId()
    {
        return $this->category_id;
    }
    public function setCategoryId($category_id)
    {
        $this->category_id = (int) $category_id;
        return $this;
    }
    
    public function getPostExcerpt()
    {
        return $this->post_excerpt;
    }
    public function setPostExcerpt($post_excerpt)
    {
        $this->post_excerpt = $post_excerpt;
        return $this;
    }
    
    public function getSeo()
    {
        return $this->seo;
    }
    public function setSeo($seo)
    {
        $this->seo = $seo;
        return $this;
    }
    
    public function getHits()
    {
        return $this->hits;
    }
    public function setHits($hits)
    {
        $this->hits = (int) $hits;
        return $this;
    }
    
    public function getFeatured()
    {
        return $this->featured;
    }
    public function setFeatured($featured)
    {
        $this->featured = (int) $featured;
        return $this;
    }
    
    public function getFeaturedImage()
    {
        return $this->featured_image;
    }
    public function setFeaturedImage($featured_image)
    {
        $this->featured_image = $featured_image;
        return $this;
    }
    
    public function getAttachment()
    {
        return $this->attachment;
    }
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
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
}