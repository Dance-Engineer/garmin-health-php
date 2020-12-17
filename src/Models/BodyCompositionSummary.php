<?php

declare(strict_types = 1);

namespace DanceEngineer\GarminHealth\Models;

use DanceEngineer\GarminHealth\Processor;

class BodyCompositionSummary extends BaseModel
{

    private ?int $muscleMass = null;

    public function muscleMass(): ?int
    {
        return $this->muscleMass;
    }

    public function boneMass(): ?int
    {
        return $this->boneMass;
    }

    public function bodyWater(): ?float
    {
        return $this->bodyWater;
    }

    public function bodyFat(): ?float
    {
        return $this->bodyFat;
    }

    public function BMI(): ?float
    {
        return $this->BMI;
    }

    public function weight(): ?int
    {
        return $this->weight;
    }

    private ?int $boneMass = null;

    private ?float $bodyWater = null;

    private ?float $bodyFat = null;

    private ?float $BMI = null;

    private ?int $weight = null;

    public function processBy(Processor $processor, string $userAccessToken): void
    {
        $processor->processBodyCompositionSummary($this, $userAccessToken);
    }

    protected function populate(array $data): void
    {
        if (array_key_exists('muscleMassInGrams', $data)) {
            $this->muscleMass = $data['muscleMassInGrams'];
        }
        if (array_key_exists('boneMassInGrams', $data)) {
            $this->boneMass = $data['boneMassInGrams'];
        }
        if (array_key_exists('bodyWaterInPercent', $data)) {
            $this->bodyWater = $data['bodyWaterInPercent'];
        }
        if (array_key_exists('bodyFatInPercent', $data)) {
            $this->bodyFat = $data['bodyFatInPercent'];
        }
        if (array_key_exists('bodyMassIndex', $data)) {
            $this->BMI = $data['bodyMassIndex'];
        }
        if (array_key_exists('weightInGrams', $data)) {
            $this->weight = $data['weightInGrams'];
        }
    }
}