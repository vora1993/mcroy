<?php

namespace Application\Entity;

interface TestimonialInterface
{
    public function getId();
    public function getName();
    public function getCompany();
    public function getPosition();
    public function getContent();
    public function getUrl();
    public function getSortOrder();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}