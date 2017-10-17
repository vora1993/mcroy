<?php

namespace Application\Entity;

interface MediaImportInterface
{
    public function getId();
    public function getTitle();
    public function getContent();
    public function getTime();
}