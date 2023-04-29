<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Response\Violation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ManagerController extends AbstractController
{
    /**
     * 设置用户的 role
     */
    #[IsGranted('ROLE_SUPER_USER')]
    #[Route('/manager/set-users-roles', methods: ['POST'])]
    public function setUsersRoles(
        Request        $request,
        UserRepository $userRepository,
        Violation      $violation
    ): JsonResponse
    {
        $userIdentifiers = (array)$request->get('user_identifiers');
        $rolesString = $request->get('roles');
        $roles = explode(',', $rolesString);

        $allowRoles = ['ROLE_MANAGER', 'ROLE_ADMIN', 'ROLE_SUPER_USER'];
        $roles = array_intersect($allowRoles, $roles);

        if (!$userIdentifiers || !$roles) {
            return $this->acceptWith($violation->withMessages('缺少参数'));
        }

        $userRepository->setRolesByIdentifiers($userIdentifiers, $roles);
        return $this->json(['message' => 'Succeed']);
    }
}
