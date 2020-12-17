<?php

declare(strict_types = 1);

namespace DanceEngineer\GarminHealth\Models;

use DanceEngineer\GarminHealth\Processor;
use DateTimeImmutable;
use DateTimeZone;

abstract class BaseModel
{
    protected ?int $userTimeOffset = null;

    protected ?DateTimeImmutable $utcStartTime = null;

    protected ?string $id = null;

    protected array $rawData;

    public function __construct(array $data)
    {
        $this->rawData = $data;
        if (array_key_exists('summaryId', $data)) {
            $this->id = $data['summaryId'];
        }
        if (array_key_exists('startTimeInSeconds', $data)) {
            $this->utcStartTime =
                new DateTimeImmutable('@'.(string)$data['startTimeInSeconds'], new DateTimeZone('UTC'));
        }
        if (array_key_exists('startTimeOffsetInSeconds', $data)) {
            $this->userTimeOffset = (int)$data['startTimeOffsetInSeconds'];
        }
        $this->populate($data);
    }

    abstract protected function populate(array $data): void;

    public function utcStartTime(): ?DateTimeImmutable
    {
        return $this->utcStartTime;
    }

    public function userTimeOffset(): ?int
    {
        return $this->userTimeOffset;
    }

    public function id(): ?string
    {
        return $this->id;
    }

    abstract public function processBy(Processor $processor, string $userAccessToken): void;

    public function rawData(): array
    {
        return $this->rawData;
    }

}