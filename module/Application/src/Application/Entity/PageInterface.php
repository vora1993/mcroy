<?php

namespace Application\Entity;

interface PageInterface
{
    public function getId();
    public function getPostAuthor();
    public function getPostDate();
    public function getPostContent();
    public function getPostTitle();
    public function getPostExcerpt();
    public function getSeo();
    public function getFeaturedImage();
    public function getHits();
    public function getFeatured();
    public function getAttachment();
    public function getSortOrder();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}