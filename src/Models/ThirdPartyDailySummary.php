<?php

declare(strict_types = 1);

namespace DanceEngineer\GarminHealth\Models;

use DanceEngineer\GarminHealth\Processor;

class ThirdPartyDailySummary extends BaseModel
{

    public function processBy(Processor $processor, string $userAccessToken): void
    {
        $processor->processThirdPartyDailySummary($this, $userAccessToken);
    }

    protected function populate(array $data): void
    {
        // TODO: Implement populate() method.
    }
}