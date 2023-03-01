<?php

namespace App\Controller;

use App\Entity\Dumpling;
use App\Entity\DumplingRequirement;
use App\Entity\Form;
use App\Entity\User;
use App\Repository\DumplingRepository;
use App\Repository\DumplingRequirementRepository;
use App\Repository\UserRepository;
use App\Response\DumplingSummary;
use App\Strategy\QueryList;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DumplingController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/dumpling/save', methods: ['POST'])]
    public function save(
        Request              $request,
        UserRepository       $userRepository,
        DumplingRepository   $dumplingRepository,
        ValidatorInterface   $validator,
        #[CurrentUser] ?User $user,
    ): JsonResponse
    {
        if ($dumplingId = $request->get('dumpling_id')) {
            $dumpling = $dumplingRepository->findOneBy(['id' => $dumplingId]);
            if (!$dumpling) {
                return $this->jsonErrors(['_violations' => ['找不到该资源']], 404);
            }
            if (!$userRepository->hasDumpling($user, $dumpling)) {
                return $this->jsonErrors(['_violations' => ['编辑权限不足']], 403);
            }
        } else {
            $dumpling = new Dumpling();
        }

        $dumpling->setTitle($request->get('title'));
        $dumpling->setSubtitle($request->get('subtitle'));
        $dumpling->setDetail($request->get('detail'));
        $dumpling->setTag($request->get('tags'));
        $dumpling->setStatus($request->get('status'));
        $dumpling->setCreatedAt(new DateTimeImmutable());
        $dumpling->setUpdatedAt(new DateTime());
        $dumpling->setUser($user);

        $errors = $validator->validate($dumpling);
        if ($errors->count()) {
            return $this->jsonErrorsForConstraints($errors);
        }

        $dumplingRepository->save($dumpling, true);

        return $this->json(['message' => 'Succeed']);
    }

    /**
     * {@see https://www.doctrine-project.org/projects/doctrine-orm/en/2.14/reference/dql-doctrine-query-language.html#dql-temporarily-change-fetch-mode}
     *
     * @param Request $request
     * @param QueryList $queryListStrategy
     * @param DumplingSummary $dumplingSummary
     * @param DumplingRepository $dumplingRepository
     * @return JsonResponse
     */
    #[Route('/dumpling/search', methods: ['GET'])]
    public function search(
        Request            $request,
        QueryList          $queryListStrategy,
        DumplingSummary    $dumplingSummary,
        DumplingRepository $dumplingRepository,
    ): JsonResponse
    {
        $keywords = $request->get('keywords');
        $builder = $dumplingRepository->searchByKeywords($keywords);
        $builder = $queryListStrategy->withRequest($request, $builder);
        $list = $builder->getQuery()
            ->setFetchMode(Dumpling::class, 'user', ClassMetadataInfo::FETCH_EAGER)
            ->getResult();
        return $this->json([
            'dumplings' => array_map(fn($d) => $dumplingSummary->withDumpling($d), $list)
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/dumpling/save-requirement', methods: ['POST'])]
    public function saveRequirement(
        Request                       $request,
        ValidatorInterface            $validator,
        DumplingRepository            $dumplingRepository,
        DumplingRequirementRepository $dumplingRequirementRepository,
    ): JsonResponse
    {
        $dumplingId = $request->get('dumpling_id');
        $dumpling = $dumplingRepository->findOneBy(['id' => $dumplingId]);
        if (!$dumpling) {
            return $this->jsonErrors(['_violations' => ['找不到资源']], 404);
        }
        if ($requirementId = $request->get('requirement_id')) {
            $dumplingRequirement = $dumplingRequirementRepository->findOneBy(['id' => $requirementId]);
            if (!$dumplingRequirement) {
                return $this->jsonErrors(['_violations' => ['找不到资源']], 404);
            }
            if ($dumplingRequirement->getDumpling()->getId() !== $dumplingId) {
                return $this->jsonErrors(['_violations' => ['禁止转让 Requirement']], 403);
            }
        } else {
            $dumplingRequirement = new DumplingRequirement();
        }
        $dumplingRequirement->setName($request->get('name'));
        $dumplingRequirement->setStatus($request->get('status'));
        $dumplingRequirement->setCreatedAt(new DateTimeImmutable());
        $dumplingRequirement->setUpdatedAt(new DateTime());
        $dumplingRequirement->setDumpling($dumpling);

        $errors = $validator->validate($dumplingRequirement);
        if ($errors->count()) {
            return $this->jsonErrorsForConstraints($errors);
        }

        $dumplingRequirementRepository->save($dumplingRequirement);

        return $this->json(['message' => 'Succeed']);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/dumpling/join', name: 'app_dumpling_join')]
    public function join(
        Request              $request,
        DumplingRepository   $dumplingRepository,
        #[CurrentUser] ?User $user,
    )
    {
        $dumplingId = $request->get('dumpling_id');
    }
}
