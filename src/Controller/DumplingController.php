<?php

namespace App\Controller;

use App\Entity\Dumpling;
use App\Entity\DumplingMember;
use App\Entity\User;
use App\Repository\DumplingMemberRepository;
use App\Repository\DumplingRepository;
use App\Repository\DumplingRequirementRepository;
use App\Repository\FormRepository;
use App\Repository\FormValidatorRepository;
use App\Repository\UserRepository;
use App\Response\DumplingSummary;
use App\Response\ListOf;
use App\Response\MemberSummary;
use App\Response\Message;
use App\Response\Violation;
use App\Service\Form as FormService;
use App\Strategy\QueryList;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
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
        Violation            $violation,
        Message              $message,
        #[CurrentUser] ?User $user,
    ): JsonResponse
    {
        if ($dumplingId = $request->get('dumpling_id')) {
            $dumpling = $dumplingRepository->findOneBy(['id' => $dumplingId]);
            if (!$dumpling) {
                return $this->acceptWith($violation->withMessages('找不到该资源'), 417);
            }

            if (!$userRepository->hasDumpling($user, $dumpling)) {
                return $this->acceptWith($violation->withMessages('编辑权限不足'), 403);
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
            return $this->acceptWith($violation->withConstraints($errors), 417);
        }

        $dumplingRepository->save($dumpling, true);

        return $this->acceptWith($message->with('Succeed'));
    }

    /**
     * {@see https://www.doctrine-project.org/projects/doctrine-orm/en/2.14/reference/dql-doctrine-query-language.html#dql-temporarily-change-fetch-mode}
     *
     * @throws ReflectionException
     */
    #[Route('/dumpling/search', methods: ['GET'])]
    public function search(
        Request            $request,
        QueryList          $queryListStrategy,
        DumplingSummary    $summary,
        DumplingRepository $dumplingRepository,
        ListOf             $listOf,
    ): JsonResponse
    {
        $keywords = $request->get('keywords');
        $builder = $dumplingRepository->searchByKeywords($keywords);
        $builder = $queryListStrategy->withRequest($request, $builder);
        $list = $builder->getQuery()
            ->setFetchMode(Dumpling::class, 'user', ClassMetadataInfo::FETCH_EAGER)
            ->getResult();
        $summaries = array_map(fn($d) => $summary->withDumpling($d)->withUser($d), $list);
        return $this->acceptWith($listOf->with($summaries, DumplingSummary::class));
    }

    /**
     * @throws Exception
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/dumpling/save-requirement', methods: ['POST'])]
    public function saveRequirement(
        Request                       $request,
        FormService                   $formService,
        LoggerInterface               $logger,
        FormRepository                $formRepository,
        ValidatorInterface            $validator,
        DumplingRepository            $dumplingRepository,
        FormValidatorRepository       $formValidatorRepository,
        DumplingRequirementRepository $requirementRepository,
        EntityManagerInterface        $em,
        Violation                     $violation,
        Message                       $message,
        #[CurrentUser] ?User          $user,
    ): JsonResponse
    {
        $dumplingId = $request->get('dumpling_id');
        $dumpling = $dumplingRepository->findOneBy(['id' => $dumplingId, 'user' => $user]);
        if (!$dumpling) {
            return $this->acceptWith($violation->withMessages('找不到资源'), 404);
        }

        $em->getConnection()->beginTransaction();
        try {
            $requirement = $requirementRepository->findOneOrNew(['id' => $request->get('id')]);
            $requirement->loadFromParameterBag($request->request)->setDumpling($dumpling);

            if ($formParam = $request->get('form')) {
                $formBag = new ParameterBag($formParam);
                $form = $formRepository->findOneOrNew(['id' => $formBag->get('id'), 'user' => $user]);
                $form->loadFromParameterBag($formBag)->setUser($user);

                $formValidator = null;
                if ($formBag->get('with_validator')) {
                    $formValidator = $formValidatorRepository->findOneOrNew(['id' => $formBag->get('validator_id')]);
                    $formValidator->loadFromParameterBag($formBag)->setUser($user)->setForm($form);
                }

                $fields = $formBag->get('fields', []);
                $formService->bindFormFields($fields, $form, $formValidator);
                $requirement->setForm($form);
            }

            if (($errors = $validator->validate($requirement))->count()) {
                return $this->acceptWith($violation->withConstraints($errors), 417);
            }
            $requirementRepository->save($requirement, true);
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            $logger->error($e->getMessage(), ['userId' => $user->getId()]);
            return $this->acceptWith($violation->withMessages('创建失败，请再次尝试'), 417);
        }

        return $this->acceptWith($message->with('Succeed'));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/dumpling/join-member', name: 'app_dumpling_join_member', methods: ['POST'])]
    public function joinMember(
        Request                  $request,
        DumplingRepository       $dumplingRepository,
        DumplingMemberRepository $dumplingMemberRepository,
        ValidatorInterface       $validator,
        Violation                $violation,
        Message                  $message,
        #[CurrentUser] ?User     $user,
    )
    {
        $dumplingId = $request->get('dumpling_id');
        $dumpling = $dumplingRepository->findOneBy(['id' => $dumplingId]);
        if (!$dumpling) {
            return $this->acceptWith($violation->withMessages('找不到资源'), 404);
        }
        $member = (new DumplingMember())->initialProperty();
        $member->setUser($user);
        $member->setNickname($user->getNickname());
        $member->setDumpling($dumpling);
        if (($errors = $validator->validate($member))->count()) {
            return $this->acceptWith($violation->withConstraints($errors), 417);
        }

        $dumplingMemberRepository->save($member, true);

        return $this->acceptWith($message->with('Succeed'));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/dumpling/set-member-nickname', name: 'app_dumpling_set_member_nickname', methods: ['POST'])]
    public function setMemberNickname(
        Request                  $request,
        Message                  $message,
        Violation                $violation,
        ValidatorInterface       $validator,
        DumplingMemberRepository $dumplingMemberRepository,
        #[CurrentUser] ?User     $user,
    )
    {
        $memberId = $request->get('member_id');
        $nickname = $request->get('nickname');

        $member = $dumplingMemberRepository->findOneBy(['id' => $memberId]);
        if (!$member) {
            return $this->acceptWith($violation->withMessages('找不到资源'), 404);
        }
        if ($member->getUser()->getId() !== $user->getId()) {
            return $this->acceptWith($violation->withMessages('无修改权限'), 403);
        }
        $member->setNickname($nickname);
        $member->setUpdatedAt(new DateTime());
        if (($errors = $validator->validate($member))->count()) {
            return $this->acceptWith($violation->withConstraints($errors), 417);
        }
        $dumplingMemberRepository->save($member, true);

        return $this->acceptWith($message->with('Succeed'));
    }

    /**
     * @throws ReflectionException
     */
    #[Route('/dumpling/search-members', methods: ['GET'])]
    public function searchMembers(
        Request                  $request,
        QueryList                $queryListStrategy,
        MemberSummary            $memberSummary,
        DumplingMemberRepository $dumplingMemberRepository,
        ListOf                   $listOf,
    ): JsonResponse
    {
        $keywords = $request->get('keywords');
        $builder = $dumplingMemberRepository->searchByKeywords($keywords);
        $builder = $queryListStrategy->withRequest($request, $builder);
        $list = $builder->getQuery()
            ->setFetchMode(DumplingMember::class, 'user', ClassMetadataInfo::FETCH_EAGER)
            ->setFetchMode(DumplingMember::class, 'dumpling', ClassMetadataInfo::FETCH_EAGER)
            ->getResult();
        $members = array_map(fn($m) => $memberSummary->withMember($m), $list);
        return $this->acceptWith($listOf->with($members, MemberSummary::class));
    }
}
