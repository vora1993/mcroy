<?php
namespace Application\Entity;

class UserFacebook implements UserFacebookInterface
{
    protected $id;
    protected $user_id;
    protected $facebook_id;
    
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }
    
    public function getUserId()
    {
        return $this->user_id;
    }
    public function setUserId($user_id)
    {
        $this->user_id = (int) $user_id;
        return $this;
    }

    public function getFacebookId()
    {
        return $this->facebook_id;
    }
    public function setFacebookId($facebook_id)
    {
        $this->facebook_id = (int) $facebook_id;
        return $this;
    }
}
