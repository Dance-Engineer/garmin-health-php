<?php

declare(strict_types = 1);

namespace DanceEngineer\GarminHealth;


use League\OAuth1\Client\Credentials\CredentialsInterface;

interface CredentialsProvider
{

    public function credentials(string $userAccessToken): ?CredentialsInterface;
}