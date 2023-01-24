<?php

namespace App\Controller;

use App\Entity\User;
use App\Interface\ObjectStorage;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use App\Service\Authentic;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    /**
     * 设置密码
     */
    #[Route('/user/set-password', methods: ['POST'])]
    public function setPassword(
        Request                     $request,
        Authentic                   $authentic,
        UserRepository              $userRepository,
        UserPasswordHasherInterface $passwordHashTool,
        #[CurrentUser] ?User         $user,
    ): JsonResponse
    {
        $password = $request->get('password');
        if ($errors = $authentic->validatePassword($password)) {
            return $this->jsonErrorsForConstraints($errors);
        }

        $user->setPassword($passwordHashTool->hashPassword($user, $password));
        $userRepository->save($user, true);

        return $this->json(['message' => 'Succeed']);
    }

    /**
     * 设置账户信息
     */
    #[Route('/user/account-data-update', methods: ['POST'])]
    public function accountDataUpdate(
        Request $request,
    ): JsonResponse
    {
        return $this->json(['message' => 'Succeed']);
    }

    /**
     * 上传头像
     */
    #[Route('/user/account-avatar-update', name: 'app_user_account_avatar_update', methods: ['POST'])]
    public function accountAvatarUpdate(
        Request             $request,
        ObjectStorage       $objectStorage,
        #[CurrentUser] ?User $user,
    ): JsonResponse
    {
        /** @var ?UploadedFile $avatar */
        $avatar = $request->files->get('avatar');
        if (!$avatar) {
            return $this->jsonErrors(['message' => 'Update failed']);
        }
        $fileName = $user->getUserIdentifier();
        $objectStorage->put("account/avatar/$fileName", $avatar->getRealPath());
        return $this->json(['message' => 'Succeed']);
    }

    /**
     * 接触绑定认证信息
     */
    #[Route('/user/unbinding-authentication', methods: ['POST'])]
    public function unbindingAuthentication(
        Request                  $request,
        AuthenticationRepository $authRepository,
        #[CurrentUser] User      $user,
    ): JsonResponse
    {
        $id = $request->get('id');
        if (!$id || !is_integer($id)) {
            return $this->jsonErrors(['message' => 'Unbinding failed']);
        }
        if ($authRepository->count(['user_id' => $user->getId()]) === 1) {
            return $this->jsonErrors(['message' => '必须至少保留一条认证信息']);
        }
        if (!$auth = $authRepository->findOneBy(['id' => $id, 'user_id' => $user->getId()])) {
            return $this->jsonErrors(['message' => 'Unbinding failed']);
        }
        $authRepository->remove($auth, true);
        return $this->json(['message' => 'Succeed']);
    }
}
