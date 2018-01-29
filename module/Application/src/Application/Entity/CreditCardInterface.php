<?php

namespace Application\Entity;

interface CreditCardInterface
{
    public function getId();
    public function getName();
    public function getLogo();
    public function getDataAttributes();
    public function getCashback();
    public function getDiscount();
    public function getPoints();
    public function getAirMiles();
    public function getColor();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
    public function getBankId();
    public function getProviderIds();
    public function getCashbackValue();
    public function getDiscountValue();
    public function getPointsValue();
    public function getAirMilesValue();
}