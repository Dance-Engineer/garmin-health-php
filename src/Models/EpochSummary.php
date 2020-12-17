<?php

declare(strict_types = 1);

namespace DanceEngineer\GarminHealth\Models;

use DanceEngineer\GarminHealth\Processor;

class EpochSummary extends BaseModel
{
    public const ACTIVITY_TYPES = [
        'running',
        'indoor_running',
        'obstacle_run',
        'street_running',
        'track_running',
        'trail_running',
        'treadmill_running',
        'virtual_run',
        'cycling',
        'bmx',
        'cyclocross',
        'downhill_biking',
        'gravel_cycling',
        'indoor_cycling',
        'mountain_biking',
        'recumbent_cycling',
        'road_biking',
        'track_cycling',
        'virtual_ride',
        'fitness_equipment',
        'bouldering',
        'elliptical',
        'indoor_cardio',
        'indoor_climbing',
        'indoor_rowing',
        'pilates',
        'stair_climbing',
        'strength_training',
        'yoga',
        'hiking',
        'swimming',
        'lap_swimming',
        'open_water_swimming',
        'walking',
        'casual_walking',
        'speed_walking',
        'transition',
        'bikeToRunTransition',
        'runToBikeTransition',
        'swimToBikeTransition',
        'motorcycling',
        'atv',
        'motocross',
        'other',
        'auto_racing',
        'boating',
        'breathwork',
        'driving_general',
        'floor_climbing',
        'flying',
        'golf',
        'hang_gliding',
        'horseback_riding',
        'hunting_fishing',
        'inline_skating',
        'mountaineerin',
        'offshore_grinding',
        'onshore_grinding',
        'paddling',
        'rc_drone',
        'rock_climbing',
        'rowing',
        'sailing',
        'sky_diving',
        'stand_up_paddleboarding',
        'stop_watch',
        'surfing',
        'tennis',
        'wakeboarding',
        'whitewater_rafting_kayaking',
        'wind_kite_surfing',
        'wingsuit_flying',
        'diving',
        'apnea_diving',
        'apnea_hunting',
        'ccr_diving',
        'gauge_diving',
        'multi_gas_diving',
        'single_gas_diving',
        'winter_sports',
        'backcountry_skiing_snowboarding_ws',
        'cross_country_skiing_ws',
        'resort_skiing_snowboarding_ws',
        'skate_skiing_ws',
        'skating_ws',
        'snow_shoe_ws',
        'snowmobiling_ws',
        'snowmobiling_ws',
    ];

    public const INTENSITIES = [
        'SEDENTARY',
        'ACTIVE',
        'HIGHLY_ACTIVE',
    ];

    private ?int $duration = null;

    private ?int $activeTime = null;

    private ?int $steps = null;

    private ?float $distance = null;

    private ?int $calories = null;

    private ?float $MET = null;

    private array $motion = [
        'meanIntensity' => null,
        'maxIntensity'  => null,
    ];

    private ?string $activityType = null;

    public function duration(): ?int
    {
        return $this->duration;
    }

    public function activeTime(): ?int
    {
        return $this->activeTime;
    }

    public function steps(): ?int
    {
        return $this->steps;
    }

    public function distance(): ?float
    {
        return $this->distance;
    }

    public function calories(): ?int
    {
        return $this->calories;
    }

    public function MET(): ?float
    {
        return $this->MET;
    }

    public function motion(): ?array
    {
        return $this->motion;
    }

    public function activityType(): ?string
    {
        return $this->activityType;
    }

    public function intensity(): ?string
    {
        return $this->intensity;
    }

    private ?string $intensity = null;

    public function processBy(Processor $processor, string $userAccessToken): void
    {
        $processor->processEpochSummary($this, $userAccessToken);
    }

    protected function populate(array $data): void
    {
        if (array_key_exists('activityType', $data)) {
            $this->activityType = $data['activityType'];
            assert(in_array($this->activityType, self::ACTIVITY_TYPES, true));
        }
        if (array_key_exists('durationInSeconds', $data)) {
            $this->duration = $data['durationInSeconds'];
        }
        if (array_key_exists('activeTimeInSeconds', $data)) {
            $this->activeTime = $data['activeTimeInSeconds'];
        }
        if (array_key_exists('steps', $data)) {
            $this->steps = $data['steps'];
        }
        if (array_key_exists('distanceInMeters', $data)) {
            $this->distance = $data['distanceInMeters'];
        }
        if (array_key_exists('activeKilocalories', $data)) {
            $this->calories = $data['activeKilocalories'];
        }
        if (array_key_exists('met', $data)) {
            $this->MET = $data['met'];
        }
        if (array_key_exists('intensity', $data)) {
            $this->intensity = $data['intensity'];
            assert(in_array($this->intensity, self::INTENSITIES, true));
        }
        if (array_key_exists('meanMotionIntensity', $data)) {
            $this->motion['meanIntensity'] = $data['meanMotionIntensity'];
        }
        if (array_key_exists('maxMotionIntensity', $data)) {
            $this->motion['maxIntensity'] = $data['maxMotionIntensity'];
        }
    }
}