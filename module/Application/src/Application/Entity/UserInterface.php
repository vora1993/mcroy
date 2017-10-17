<?php
namespace Application\Entity;

interface UserInterface
{
    public function getId();
    public function getRoleId();
    public function getUsername();
    public function getEmail();
    public function getFirstName();
    public function getLastName();
    public function getDisplayName();
    public function getPassword();
    public function getIdentity();
    public function getCredential();
    public function getPhone();
    public function getDateOfBirth();
    public function getGender();
    public function getCompanyName();
    public function getRef();
    public function getNewsletter();
    public function getAvatar();
    public function getDescription();
    public function getToken();
    public function getDateAdded();
    public function getDateModified();
    public function getStatus();
}
