<?php

namespace toubilib\core\domain\entities\auth;

use toubilib\core\domain\exceptions\AuthenticationException;


interface AuthServiceInterface
{
   
    public function authenticate(string $email, string $password): UserProfile;

   
    public function userExists(string $email): bool;
}