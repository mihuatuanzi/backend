<?php

namespace App\Response;

use App\Entity\Dumpling;
use App\Entity\User;
use App\Interface\StructureResponse;

class DumplingSummary implements StructureResponse
{
    const KEY_SINGULAR = 'dumpling_summary';
    const KEY_PLURAL = 'dumpling_summaries';

    public int $id;
    public string $title;
    public string $subtitle;
    public ?int $createdAt;
    public UserSummary $user_summary;

    public function __construct(
        private readonly UserSummary $userSummary
    )
    {
    }

    public function withDumpling(Dumpling $dumpling): self
    {
        $this->id = $dumpling->getId();
        $this->title = $dumpling->getTitle();
        $this->subtitle = $dumpling->getSubtitle();
        $this->createdAt = $dumpling->getCreatedAt()->getTimestamp();
        return $this;
    }

    public function withUser(Dumpling $dumpling): self
    {
        $this->user_summary = $this->userSummary->withUser($dumpling->getUser());
        return $this;
    }
}
