<?php

declare(strict_types=1);

namespace Vdlp\Sitemap\Classes\Dto;

use Carbon\Carbon;
use Vdlp\Sitemap\Classes\Contracts\Dto;
use Vdlp\Sitemap\Classes\Exceptions\InvalidPriority;

final class Definition implements Dto
{
    public const CHANGE_FREQUENCY_ALWAYS = 'always';
    public const CHANGE_FREQUENCY_HOURLY = 'hourly';
    public const CHANGE_FREQUENCY_DAILY = 'daily';
    public const CHANGE_FREQUENCY_WEEKLY = 'weekly';
    public const CHANGE_FREQUENCY_MONTHLY = 'monthly';
    public const CHANGE_FREQUENCY_YEARLY = 'yearly';
    public const CHANGE_FREQUENCY_NEVER = 'never';

    private ?string $url = null;
    private ?int $priority = null;
    private ?string $changeFrequency = null;
    private ?Carbon $modifiedAt = null;

    /**
     * @var ImageDefinition[]
     */
    private array $images = [];

    /**
     * @throws InvalidPriority
     */
    public static function fromArray(array $data): Dto
    {
        return (new self())->setUrl($data['url'] ?? null)
            ->setPriority($data['priority'] ?? null)
            ->setChangeFrequency($data['change_frequency'] ?? null)
            ->setModifiedAt($data['modified_at'] ?? null);
    }

    public function setUrl(?string $url): Definition
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @throws InvalidPriority
     */
    public function setPriority(?int $priority): Definition
    {
        if ($priority >= 1 && $priority <= 10) {
            $this->priority = $priority;

            return $this;
        }

        throw new InvalidPriority();
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function getPriorityFloat(): ?float
    {
        if ($this->priority === null) {
            return null;
        }

        return (float) $this->priority / 10;
    }

    public function setChangeFrequency(?string $changeFrequency): Definition
    {
        $this->changeFrequency = $changeFrequency;

        return $this;
    }

    public function getChangeFrequency(): ?string
    {
        return $this->changeFrequency;
    }

    public function setModifiedAt(?Carbon $modifiedAt): Definition
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getModifiedAt(): ?Carbon
    {
        return $this->modifiedAt;
    }

    /**
     * @return ImageDefinition[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param ImageDefinition[] $images
     */
    public function setImages(array $images): Definition
    {
        $this->images = $images;

        return $this;
    }

    public function addImage(ImageDefinition $image): Definition
    {
        $this->images[] = $image;

        return $this;
    }
}
