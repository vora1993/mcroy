<?php
namespace Application\Entity;

class User implements UserInterface
{
    protected $id;
    protected $role_id;
    protected $username;
    protected $email;
    protected $phone;
    protected $firstname;
    protected $lastname;
    protected $displayName;
    protected $password;
    protected $identity;
    protected $credential;
    protected $date_of_birth;
    protected $gender;
    protected $company_name;
    protected $ref;
    protected $newsletter;
    protected $avatar;
    protected $description;
    protected $token;
    protected $date_added;
    protected $date_mofified;
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
    
    public function getRoleId()
    {
        return $this->role_id;
    }
    public function setRoleId($role_id)
    {
        $this->role_id = (int) $role_id;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
    
    public function getPhone()
    {
        return $this->phone;
    }
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }
    
    public function getFirstName()
    {
        return $this->firstname;
    }
    public function setFirstName($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }
    
    public function getLastName()
    {
        return $this->lastname;
    }
    public function setLastName($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }
    
    public function getIdentity()
    {
        return $this->identity;
    }
    public function setIdentity($identity)
    {
        $this->identity = $identity;
        return $this;
    }
    
    public function getCredential()
    {
        return $this->credential;
    }
    public function setCredential($credential)
    {
        $this->credential = $credential;
        return $this;
    }
    
    public function getAvatar()
    {
        return $this->avatar;
    }
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
        return $this;
    }
    
    public function getDateOfBirth()
    {
        return $this->date_of_birth;
    }
    public function setDateOfBirth($date_of_birth)
    {
        $this->date_of_birth = $date_of_birth;
        return $this;
    }
    
    public function getGender()
    {
        return $this->gender;
    }
    public function setGender($gender)
    {
        $this->gender = (int) $gender;
        return $this;
    }
    
    public function getCompanyName()
    {
        return $this->company_name;
    }
    public function setCompanyName($company_name)
    {
        $this->company_name = $company_name;
        return $this;
    }
    
    public function getRef()
    {
        return $this->ref;
    }
    public function setRef($ref)
    {
        $this->ref = $ref;
        return $this;
    }
    
    public function getNewsletter()
    {
        return $this->newsletter;
    }
    public function setNewsletter($newsletter)
    {
        $this->newsletter = (int) $newsletter;
        return $this;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    
    public function getToken()
    {
        return $this->token;
    }
    public function setToken($token)
    {
        $this->token = $token;
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
        return $this->date_mofified;
    }
    public function setDateModified($date_mofified)
    {
        $this->date_mofified = $date_mofified;
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
