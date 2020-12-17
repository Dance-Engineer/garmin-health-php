<?php

declare(strict_types = 1);

namespace DanceEngineer\GarminHealth;

use DateTimeImmutable;
use League\OAuth1\Client\Credentials\CredentialsInterface;

final class PingClient
{
    private Client $baseClient;

    private CredentialsProvider $credentialsProvider;

    public function __construct(Client $baseClient, CredentialsProvider $credentialsProvider)
    {
        $this->baseClient = $baseClient;
        $this->credentialsProvider = $credentialsProvider;
    }

    /**
     * @throws \RuntimeException
     */
    public function processPing(string $userAccessToken, string $callback): void
    {
        $credentials = $this->credentialsProvider->credentials($userAccessToken);
        if ($credentials === null) {
            return;
        }
        $callbackResponse = $this->baseClient->call($credentials, $callback)
            ->getBody()
            ->getContents();

        try {
            $this->baseClient->processRequestBody(
                $callbackResponse,
                $userAccessToken,
                $callback
            );
        } catch (\InvalidArgumentException $exception) {
            throw new \RuntimeException('Unable to process Polar Response', 0, $exception);
        }
    }

    /**
     * @throws \RuntimeException
     */
    public function initiateBackFill(
        CredentialsInterface $credentials,
        string $area,
        DateTimeImmutable $start,
        DateTimeImmutable $end
    ): void {
        $this->baseClient->initiateBackFill($credentials, $area, $start, $end);
    }

}