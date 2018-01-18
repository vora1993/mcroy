<?php

namespace Application\Entity;

interface CreditCardProviderInterface
{
    public function getId();
    public function getName();
    public function getLogo();
    public function getStatus();
}