<?php

declare(strict_types = 1);

namespace DanceEngineer\GarminHealth\Models;

use DanceEngineer\GarminHealth\Processor;
use DateTimeImmutable;
use DateTimeZone;
use DusanKasan\Knapsack\Collection;

class SleepSummary extends BaseModel
{
    public const VALIDATION_MANUAL             = 'MANUAL';
    public const VALIDATION_DEVICE             = 'DEVICE';
    public const VALIDATION_AUTO_TENTATIVE     = 'AUTO_TENTATIVE';
    public const VALIDATION_AUTO_FINAL         = 'AUTO_FINAL';
    public const VALIDATION_AUTO_MANUAL        = 'AUTO_MANUAL';
    public const VALIDATION_ENHANCED_TENTATIVE = 'ENHANCED_TENTATIVE';
    public const VALIDATION_ENHANCED_FINAL     = 'ENHANCED_FINAL';

    private ?string $validationStatus = null;

    private ?DateTimeImmutable $calendarDate = null;

    /**
     * @var array{
     *     total: ?int,
     *     unmeasurable: ?int,
     *     deep: ?int,
     *     light: ?int,
     *     rem: ?int,
     *     awake: ?int
     * }
     */
    private array $durations = [
        'total'        => null,
        'unmeasurable' => null,
        'deep'         => null,
        'light'        => null,
        'rem'          => null,
        'awake'        => null,
    ];

    private array $sleepLevelsMap = [];

    private array $sleepRespiration = [];

    private array $sleepSpO2 = [];

    public function processBy(Processor $processor, string $userAccessToken): void
    {
        $processor->processSleepSummary($this, $userAccessToken);
    }


    public function validationStatus(): ?string
    {
        return $this->validationStatus;
    }

    public function calendarDate(): ?DateTimeImmutable
    {
        return $this->calendarDate;
    }

    /**
     * @return array{
     *     total: ?int,
     *     unmeasurable: ?int,
     *     deep: ?int,
     *     light: ?int,
     *     rem: ?int,
     *     awake: ?int
     * }
     */
    public function durations(): array
    {
        return $this->durations;
    }

    public function sleepLevelsMap(): array
    {
        return $this->sleepLevelsMap;
    }

    public function sleepRespiration(): array
    {
        return $this->sleepRespiration;
    }

    public function sleepSpO2(): array
    {
        return $this->sleepSpO2;
    }

    protected function populate(array $data): void
    {
        if (array_key_exists('calendarDate', $data)) {
            $this->calendarDate = new DateTimeImmutable($data['calendarDate'], new DateTimeZone('UTC'));
        }
        if (array_key_exists('durationInSeconds', $data)) {
            $this->durations['total'] = $data['durationInSeconds'];
        }
        if (array_key_exists('unmeasurableSleepInSeconds', $data)) {
            $this->durations['unmeasurable'] = $data['unmeasurableSleepInSeconds'];
        }
        if (array_key_exists('deepSleepDurationInSeconds', $data)) {
            $this->durations['deep'] = $data['deepSleepDurationInSeconds'];
        }
        if (array_key_exists('lightSleepDurationInSeconds', $data)) {
            $this->durations['light'] = $data['lightSleepDurationInSeconds'];
        }
        if (array_key_exists('remSleepInSeconds', $data)) {
            $this->durations['rem'] = $data['remSleepInSeconds'];
        }
        if (array_key_exists('awakeDurationInSeconds', $data)) {
            $this->durations['awake'] = $data['awakeDurationInSeconds'];
        }
        if (array_key_exists('sleepLevelsMap', $data)) {
            $this->sleepLevelsMap = Collection::from($data['sleepLevelsMap'])
                ->map(
                    function (array $area): array {
                        return Collection::from($area)
                            ->map(
                                function (array $instance): array {
                                    return [
                                        'start' => new DateTimeImmutable('@'.(string)$instance['startTimeInSeconds']),
                                        'end'   => new DateTimeImmutable('@'.(string)$instance['endTimeInSeconds']),
                                    ];
                                }
                            )
                            ->toArray();
                    }
                )
                ->toArray();
        }
        if (array_key_exists('validation', $data)) {
            $this->validationStatus = $data['validation'];
        }
        if (array_key_exists('timeOffsetSleepRespiration', $data)) {
            $this->sleepRespiration = $data['timeOffsetSleepRespiration'];
        }
        if (array_key_exists('timeOffsetSleepSpo2', $data)) {
            $this->sleepSpO2 = $data['timeOffsetSleepSpo2'];
        }
    }
}