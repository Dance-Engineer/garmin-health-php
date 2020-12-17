<?php

declare(strict_types = 1);

namespace DanceEngineer\GarminHealth;

use DanceEngineer\GarminHealth\Models\{ActivityDetailsSummary,
    ActivityFile,
    ActivitySummary,
    BaseModel,
    BodyCompositionSummary,
    DailySummary,
    EpochSummary,
    ManuallyUpdatedActivitySummary,
    MenstrualCycleTrackingSummary,
    MoveIQSummary,
    PulseOxSummary,
    RespirationSummary,
    SleepSummary,
    StressDetailsSummary,
    ThirdPartyDailySummary,
    UserMetricsSummary
};
use DateTimeImmutable;
use DateTimeZone;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use JsonException;
use League\OAuth1\Client\Credentials\CredentialsInterface;
use League\OAuth1\Client\Server\Server;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

final class Client
{
    private const REGISTRATION_URL    = 'https://healthapi.garmin.com/wellness-api/rest/user/registration';
    private const API_URL_BASE        = 'https://healthapi.garmin.com/wellness-api/rest/';
    private const BACK_FILL_URL_BASE  = 'https://healthapi.garmin.com/wellness-api/rest/backfill/';
    private const BACK_FILL_DAY_RANGE = 90;

    public const DAILIES                     = 'dailies';
    public const THIRD_PARTY_DAILIES         = 'thirdPartyDailies';
    public const ACTIVITIES                  = 'activities';
    public const MANUALLY_UPDATED_ACTIVITIES = 'manuallyUpdatedActivities';
    public const ACTIVITY_DETAILS            = 'activityDetails';
    public const ACTIVITY_FILE               = 'activityFile';
    public const EPOCHS                      = 'epochs';
    public const SLEEPS                      = 'sleeps';
    public const BODY_COMPS                  = 'bodyComps';
    public const STRESS_DETAILS              = 'stressDetails';
    public const USER_METRICS                = 'userMetrics';
    public const MOVE_IQ                     = 'moveiq';
    public const PULSE_OX                    = 'pulseOx';
    public const MENSTRUAL_CYCLE_TRACKING    = 'mct';
    public const RESPIRATION                 = 'respiration';

    private const ENDPOINTS = [
        self::API_URL_BASE.self::DAILIES                     => DailySummary::class,
        self::API_URL_BASE.self::THIRD_PARTY_DAILIES         => ThirdPartyDailySummary::class,
        self::API_URL_BASE.self::ACTIVITIES                  => ActivitySummary::class,
        self::API_URL_BASE.self::MANUALLY_UPDATED_ACTIVITIES => ManuallyUpdatedActivitySummary::class,
        self::API_URL_BASE.self::ACTIVITY_DETAILS            => ActivityDetailsSummary::class,
        self::API_URL_BASE.self::ACTIVITY_FILE               => ActivityFile::class,
        self::API_URL_BASE.self::EPOCHS                      => EpochSummary::class,
        self::API_URL_BASE.self::SLEEPS                      => SleepSummary::class,
        self::API_URL_BASE.self::BODY_COMPS                  => BodyCompositionSummary::class,
        self::API_URL_BASE.self::STRESS_DETAILS              => StressDetailsSummary::class,
        self::API_URL_BASE.self::USER_METRICS                => UserMetricsSummary::class,
        self::API_URL_BASE.self::MOVE_IQ                     => MoveIQSummary::class,
        self::API_URL_BASE.self::PULSE_OX                    => PulseOxSummary::class,
        self::API_URL_BASE.self::MENSTRUAL_CYCLE_TRACKING    => MenstrualCycleTrackingSummary::class,
        self::API_URL_BASE.self::RESPIRATION                 => RespirationSummary::class,
    ];

    public const BACK_FILL_AREAS = [
        self::DAILIES,
        self::EPOCHS,
        self::ACTIVITIES,
        self::ACTIVITY_DETAILS,
        self::SLEEPS,
        self::BODY_COMPS,
        self::STRESS_DETAILS,
        self::USER_METRICS,
        self::MOVE_IQ,
        self::PULSE_OX,
        self::RESPIRATION,
    ];

    private Processor $processor;

    private Server $server;

    public function __construct(Server $server, Processor $processor)
    {
        $this->server    = $server;
        $this->processor = $processor;
    }

    /**
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function processRequestBody(string $requestBody, string $userAccessToken, ?string $url = null): void
    {
        try {
            $jsonBody = json_decode($requestBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('Unable to parse the request body.', 0, $exception);
        }
        foreach ($jsonBody as $item) {
            $this->modelFromData($item, $url)
                ->processBy($this->processor, $userAccessToken);
        }
    }

    /**
     * @throws \RuntimeException
     */
    private function modelFromData(array $item, string $url): BaseModel
    {
        foreach (self::ENDPOINTS as $endpoint => $dataClass) {
            if (strncmp($url, $endpoint, strlen($endpoint)) === 0) {// starts with
                return new $dataClass($item);
            }
        }
        throw new RuntimeException('Unrecognized model');
    }

    /**
     * @throws \RuntimeException
     */
    public function deregister(CredentialsInterface $credentials): void
    {
        try {
            $this->server->createHttpClient()
                ->delete(
                    self::REGISTRATION_URL,
                    [
                        'headers' => $this->server->getHeaders($credentials, 'DELETE', self::REGISTRATION_URL),
                    ]
                );
        } catch (GuzzleException | BadResponseException $e) {
            throw new RuntimeException('Unable to de-register user.', 0, $e);
        }
    }

    /**
     * @throws \RuntimeException
     */
    public function call(CredentialsInterface $credentials, string $url): ResponseInterface
    {
        try {
            return $this->server->createHttpClient()
                ->get(
                    $url,
                    [
                        'headers' => $this->server->getHeaders($credentials, 'GET', $url),
                    ]
                );
        } catch (GuzzleException | BadResponseException $e) {
            throw new RuntimeException('Unable to process call to Garmin API.', 0, $e);
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
        if ($end > new DateTimeImmutable('now')) {
            throw new InvalidArgumentException('The ned have to be in the past or in the present, not in the future.');
        }
        if ($end->diff($start, true)->days > self::BACK_FILL_DAY_RANGE) {
            throw new InvalidArgumentException('The maximum date range is'.self::BACK_FILL_DAY_RANGE.' days.');
        }
        if ($start > $end) {
            throw new InvalidArgumentException('The end time has to be after the start time.');
        }
        if (!in_array($area, self::BACK_FILL_AREAS, true)) {
            throw new InvalidArgumentException(
                'Back-fill area "'.$area.'" not supported. Supported values are: ['.implode(
                    ', ',
                    self::BACK_FILL_AREAS
                ).'].'
            );
        }

        $url = self::buildUrl(
            self::BACK_FILL_URL_BASE.$area,
            http_build_query(
                [
                    'summaryStartTimeInSeconds' => $start->setTimezone(new DateTimeZone('UTC'))
                        ->getTimestamp(),
                    'summaryEndTimeInSeconds'   => $end->setTimezone(new DateTimeZone('UTC'))
                        ->getTimestamp(),
                ]
            )
        );

        $client = $this->server->createHttpClient();

        $headers = $this->server->getHeaders($credentials, 'GET', $url);

        try {
            $client->get(
                $url,
                [
                    'headers' => $headers,
                ]
            );
        } catch (GuzzleException | BadResponseException $e) {
            throw new RuntimeException('Unable to initiate back-fill.', 0, $e);
        }
    }

    private static function buildUrl(string $host, string $queryString): string
    {
        return $host.(strpos($host, '?') !== false ? '&' : '?').$queryString;
    }

}