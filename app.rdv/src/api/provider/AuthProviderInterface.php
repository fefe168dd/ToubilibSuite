<?php

namespace toubilib\api\provider;

use toubilib\core\domain\entities\auth\AuthTokenDTO;
use toubilib\core\domain\exceptions\AuthenticationException;


interface AuthProviderInterface
{
   
    public function signin(string $email, string $password): AuthTokenDTO;

   
    public function refresh(string $refreshToken): AuthTokenDTO;

    
    public function validateToken(string $accessToken): AuthTokenDTO;
}
