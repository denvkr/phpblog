<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpBlog;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Query\Expr\Base;

class phpBlogUser extends Base implements UserInterface {
    
    public function getRoles() {
        return $this->credentials;
    }

    public function getPassword() {
        return $this->passw;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function getUsername() {
        return $this->uname;
    }

    public function eraseCredentials() {
        
    }

    public function equals(UserInterface $user) {
        return $this->getUsername() === $user->getUsername();
    }
    
    public function __toString() {
        return $this->uname;
    }
    
}

