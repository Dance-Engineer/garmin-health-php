<?php

declare(strict_types = 1);

namespace DanceEngineer\GarminHealth;

interface Processor
{
    public function processDailySummary(Models\DailySummary $dailySummary, string $userAccessToken): void;
    public function processThirdPartyDailySummary(Models\ThirdPartyDailySummary $dailySummary, string $userAccessToken): void;
    public function processActivitySummary(Models\ActivitySummary $activitySummary, string $userAccessToken): void;
    public function processManuallyUpdatedActivitySummary(Models\ManuallyUpdatedActivitySummary $activitySummary, string $userAccessToken): void;
    public function processActivityDetailsSummary(Models\ActivityDetailsSummary $activityDetailsSummary, string $userAccessToken): void;
    public function processActivityFile(Models\ActivityFile $activityFile, string $userAccessToken): void;
    public function processEpochSummary(Models\EpochSummary $epochSummary, string $userAccessToken): void;
    public function processSleepSummary(Models\SleepSummary $sleepSummary, string $userAccessToken): void;
    public function processBodyCompositionSummary(Models\BodyCompositionSummary $bodyCompositionSummary, string $userAccessToken): void;
    public function processStressDetailsSummary(Models\StressDetailsSummary $stressDetailsSummary, string $userAccessToken): void;
    public function processUserMetricsSummary(Models\UserMetricsSummary $userMetricsSummary, string $userAccessToken): void;
    public function processMoveIQSummary(Models\MoveIQSummary $moveIQSummary, string $userAccessToken): void;
    public function processPulseOxSummary(Models\PulseOxSummary $pulseOxSummary, string $userAccessToken): void;
    public function processMenstrualCycleTrackingSummary(Models\MenstrualCycleTrackingSummary $menstrualCycleTrackingSummary, string $userAccessToken): void;
    public function processRespirationSummary(Models\RespirationSummary $respirationSummary, string $userAccessToken): void;

}