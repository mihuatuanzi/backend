<?php

namespace App\Response;

use App\Entity\Dumpling;
use App\Interface\StructureResponse;

class DumplingSummary implements StructureResponse
{
    const SINGULAR = 'dumpling_summary';
    const PLURAL = 'dumpling_summaries';

    public string $id;
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
        $this->{UserSummary::SINGULAR} = $this->userSummary->withUser($dumpling->getUser());
        return $this;
    }
}
