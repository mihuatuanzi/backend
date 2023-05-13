<?php

namespace App\Response;

use App\Entity\DumplingMember;
use App\Interface\StructureResponse;

class MemberSummary implements StructureResponse
{
    const KEY_SINGULAR = 'dumpling_member';
    const KEY_PLURAL = 'dumpling_members';

    public int $id;
    public string $nickname;
    public array $roles;
    public string $status_mask;

    public function __construct(
        public readonly UserSummary $user_summary,
        public readonly DumplingSummary $dumpling_summary,
    )
    {
    }

    public function withMember(DumplingMember $dumplingMember): self
    {
        $this->id = $dumplingMember->getId();
        $this->nickname = $dumplingMember->getNickname();
        $this->roles = $dumplingMember->getRoles();
        $this->status_mask = $dumplingMember->getStatusMask();
        $this->user_summary->withUser($dumplingMember->getUser());
        $this->dumpling_summary->withDumpling($dumplingMember->getDumpling());
        return $this;
    }
}
