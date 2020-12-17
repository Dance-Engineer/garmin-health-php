<?php

declare(strict_types = 1);

namespace DanceEngineer\GarminHealth\Models;

use DanceEngineer\GarminHealth\Processor;
use DateTimeImmutable;
use DateTimeZone;

class DailySummary extends BaseModel
{

    public const STRESS_UNKNOWN              = 'unknown';
    public const STRESS_CALM                 = 'calm';
    public const STRESS_BALANCED             = 'balanced';
    public const STRESS_STRESSFUL            = 'stressful';
    public const STRESS_VERY_STRESSFUL       = 'very_stressful';
    public const STRESS_CALM_AWAKE           = 'calm_awake';
    public const STRESS_BALANCED_AWAKE       = 'balanced_awake';
    public const STRESS_STRESSFUL_AWAKE      = 'stressful_awake';
    public const STRESS_VERY_STRESSFUL_AWAKE = 'very_stressful_awake';

    private const STRESS_LEVELS = [
        -1  => 'Not enough data',
        25  => 'Rest',
        50  => 'Low',
        75  => 'Medium',
        100 => 'High',
    ];

    private ?DateTimeImmutable $calendarDate = null;

    private ?int $duration = null;

    private ?int $steps = null;

    private ?float $distance = null;

    private array $calories = [
        'active'   => null,
        'bmr'      => null,
        'consumed' => null,
        'netGoal'  => null,
    ];

    private ?int $activeTime = null;

    private array $activityIntensity = [
        'moderate' => null,
        'rigorous' => null,
    ];

    private ?int $floors = null;

    private array $heartRate = [
        'min'     => null,
        'average' => null,
        'max'     => null,
        'resting' => null,
        'samples' => [],
    ];

    private array $stress = [
        'level'     => [
            'average'     => null,
            'averageText' => self::STRESS_LEVELS[-1],
            'max'         => null,
        ],
        'duration'  => [
            'rest'      => null,
            'low'       => null,
            'medium'    => null,
            'high'      => null,
            'stressful' => null,
            'activity'  => null,
        ],
        'qualifier' => null,
    ];

    private ?int $stepsGoal;

    private ?int $floorsGoal;

    private ?int $activityGoal;

    public function floors(): ?int
    {
        return $this->floors;
    }

    public function activityIntensity(): array
    {
        return $this->activityIntensity;
    }

    public function calories(): array
    {
        return $this->calories;
    }

    public function activeTime(): ?int
    {
        return $this->activeTime;
    }

    public function distance(): ?float
    {
        return $this->distance;
    }

    public function steps(): ?int
    {
        return $this->steps;
    }

    public function duration(): ?int
    {
        return $this->duration;
    }

    public function calendarDate(): ?DateTimeImmutable
    {
        return $this->calendarDate;
    }

    public function heartRate(): array
    {
        return $this->heartRate;
    }

    public function stress(): array
    {
        return $this->stress;
    }

    public function stepsGoal(): ?int
    {
        return $this->stepsGoal;
    }

    public function floorsGoal(): ?int
    {
        return $this->floorsGoal;
    }

    public function activityGoal(): ?int
    {
        return $this->activityGoal;
    }

    protected function populate(array $data): void
    {
        if (array_key_exists('calendarDate', $data)) {
            $this->calendarDate = new DateTimeImmutable($data['calendarDate'], new DateTimeZone('UTC'));
        }
        if (array_key_exists('durationInSeconds', $data)) {
            $this->duration = $data['durationInSeconds'];
        }
        if (array_key_exists('steps', $data)) {
            $this->steps = $data['steps'];
        }
        if (array_key_exists('distanceInMeters', $data)) {
            $this->distance = $data['distanceInMeters'];
        }
        if (array_key_exists('activeTimeInSeconds', $data)) {
            $this->activeTime = $data['activeTimeInSeconds'];
        }
        if (array_key_exists('activeKilocalories', $data)) {
            $this->calories['active'] = $data['activeKilocalories'];
        }
        if (array_key_exists('bmrKilocalories', $data)) {
            $this->calories['bmr'] = $data['bmrKilocalories'];
        }
        if (array_key_exists('consumedCalories', $data)) {
            $this->calories['consumed'] = $data['consumedCalories'];
        }
        if (array_key_exists('moderateIntensityDurationInSeconds', $data)) {
            $this->activityIntensity['moderate'] = $data['moderateIntensityDurationInSeconds'];
        }
        if (array_key_exists('vigorousIntensityDurationInSeconds', $data)) {
            $this->activityIntensity['vigorous'] = $data['vigorousIntensityDurationInSeconds'];
        }
        if (array_key_exists('floorsClimbed', $data)) {
            $this->floors = $data['floorsClimbed'];
        }
        if (array_key_exists('minHeartRateInBeatsPerMinute', $data)) {
            $this->heartRate['min'] = $data['minHeartRateInBeatsPerMinute'];
        }
        if (array_key_exists('averageHeartRateInBeatsPerMinute', $data)) {
            $this->heartRate['average'] = $data['averageHeartRateInBeatsPerMinute'];
        }
        if (array_key_exists('maxHeartRateInBeatsPerMinute', $data)) {
            $this->heartRate['max'] = $data['maxHeartRateInBeatsPerMinute'];
        }
        if (array_key_exists('restingHeartRateInBeatsPerMinute', $data)) {
            $this->heartRate['resting'] = $data['restingHeartRateInBeatsPerMinute'];
        }
        if (array_key_exists('timeOffsetHeartRateSamples', $data)) {
            $this->heartRate['samples'] = $data['timeOffsetHeartRateSamples'];
        }
        if (array_key_exists('averageStressLevel', $data)) {
            $this->stress['level']['average']     = $data['averageStressLevel'];
            $this->stress['level']['averageText'] = self::stressTextFromLevel($data['averageStressLevel']);
        }
        if (array_key_exists('maxStressLevel', $data)) {
            $this->stress['level']['max'] = $data['maxStressLevel'];
        }
        if (array_key_exists('stressDurationInSeconds', $data)) {
            $this->stress['duration']['stressful'] = $data['stressDurationInSeconds'];
        }
        if (array_key_exists('restStressDurationInSeconds', $data)) {
            $this->stress['duration']['rest'] = $data['restStressDurationInSeconds'];
        }
        if (array_key_exists('activityStressDurationInSeconds', $data)) {
            $this->stress['duration']['activity'] = $data['activityStressDurationInSeconds'];
        }
        if (array_key_exists('lowStressDurationInSeconds', $data)) {
            $this->stress['duration']['low'] = $data['lowStressDurationInSeconds'];
        }
        if (array_key_exists('mediumStressDurationInSeconds', $data)) {
            $this->stress['duration']['medium'] = $data['mediumStressDurationInSeconds'];
        }
        if (array_key_exists('highStressDurationInSeconds', $data)) {
            $this->stress['duration']['high'] = $data['highStressDurationInSeconds'];
        }
        if (array_key_exists('stressQualifier', $data)) {
            $this->stress['qualifier'] = $data['stressQualifier'];
        }
        if (array_key_exists('stepsGoal', $data)) {
            $this->stepsGoal = $data['stepsGoal'];
        }
        if (array_key_exists('netKilocaloriesGoal', $data)) {
            $this->calories['netGoal'] = $data['netKilocaloriesGoal'];
        }
        if (array_key_exists('intensityDurationGoalInSeconds', $data)) {
            $this->activityGoal = $data['intensityDurationGoalInSeconds'];
        }
        if (array_key_exists('floorsClimbedGoal', $data)) {
            $this->floorsGoal = $data['floorsClimbedGoal'];
        }
    }

    private static function stressTextFromLevel(?int $level): string
    {
        if ($level === null) {
            return self::STRESS_LEVELS[-1];
        }
        foreach (self::STRESS_LEVELS as $cutoff => $text) {
            if ($level <= $cutoff) {
                return $text;
            }
        }
        return self::STRESS_LEVELS[-1];
    }

    public function processBy(Processor $processor, string $userAccessToken): void
    {
        $processor->processDailySummary($this, $userAccessToken);
    }

}