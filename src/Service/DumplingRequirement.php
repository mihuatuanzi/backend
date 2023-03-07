<?php

namespace App\Service;

use App\Entity\Dumpling;
use App\Entity\DumplingRequirement as Requirement;
use App\Exception\StructuredException;
use App\Repository\DumplingRequirementRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\ParameterBag;

readonly class DumplingRequirement
{
    public function __construct(
        private DumplingRequirementRepository $requirementRepository
    )
    {
    }

    public function makeRequirementByRequest(ParameterBag $request, Dumpling $dumpling, \App\Entity\Form $form): Requirement
    {
        if ($requirementId = $request->get('requirement_id')) {
            $requirement = $this->requirementRepository->findOneBy(['id' => $requirementId]);
            if (!$requirement) {
                throw new StructuredException(['_violations' => ['找不到资源']], 404);
            }
            if ($requirement->getDumpling()->getId() !== $dumpling->getId()) {
                throw new StructuredException(['_violations' => ['禁止转让 Requirement']], 403);
            }
        } else {
            $requirement = new Requirement();
        }

        $requirement->setName($request->get('name'));
        $requirement->setStatus($request->get('status'));
        $requirement->setCreatedAt(new DateTimeImmutable());
        $requirement->setUpdatedAt(new DateTime());
        $requirement->setDumpling($dumpling);
        $requirement->setForm($form);
        return $requirement;
    }
}
