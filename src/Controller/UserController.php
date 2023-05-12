<?php

namespace App\Controller;

use App\Config\UserGenderType;
use App\Entity\User;
use App\Interface\ObjectStorage;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use App\Response\ListOf;
use App\Response\Message;
use App\Response\UserSummary;
use App\Response\Violation;
use App\Service\Authentic;
use App\Strategy\QueryList;
use ReflectionException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @throws ReflectionException
     */
    #[Route('/user/search', methods: ['GET'])]
    public function search(
        Request        $request,
        UserSummary    $userSummary,
        UserRepository $userRepository,
        QueryList      $queryListStrategy,
        ListOf         $listOf,
    ): JsonResponse
    {
        $keywords = $request->get('keywords');
        $builder = $userRepository->searchByKeywords($keywords);
        $builder = $queryListStrategy->withRequest($request, $builder, [
            'user_exp' => 'u.exp'
        ]);
        $users = $builder->getQuery()->getResult();

        $userSummaries = array_map(fn($u) => $userSummary->withUser($u), $users);
        return $this->acceptWith($listOf->with($userSummaries, UserSummary::class));
    }

    /**
     * 设置密码
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/user/set-password', methods: ['POST'])]
    public function setPassword(
        Request                     $request,
        Authentic                   $authentic,
        UserRepository              $userRepository,
        UserPasswordHasherInterface $passwordHashTool,
        Violation                   $violation,
        Message                     $message,
        #[CurrentUser] ?User        $user,
    ): JsonResponse
    {
        $password = $request->get('password');
        if ($errors = $authentic->validatePassword($password)) {
            return $this->acceptWith($violation->withConstraints($errors), 417);
        }

        $user->setPassword($passwordHashTool->hashPassword($user, $password));
        $userRepository->save($user, true);

        return $this->acceptWith($message->with('Succeed'));
    }

    /**
     * 更新账户信息
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/user/account-data-update', methods: ['POST'])]
    public function accountDataUpdate(
        Request              $request,
        UserSummary          $userSummary,
        UserRepository       $userRepository,
        ValidatorInterface   $validator,
        Violation            $violation,
        #[CurrentUser] ?User $user,
    ): JsonResponse
    {
        $user->setNickname($request->get('nickname'));
        $user->setGender(UserGenderType::tryFrom($request->get('gender')));
        $user->setSignature($request->get('signature'));

        $errors = $validator->validate($user);
        if ($errors->count()) {
            return $this->acceptWith($violation->withConstraints($errors), 417);
        }
        $userRepository->save($user, true);

        return $this->acceptWith($userSummary->withUser($user));
    }

    /**
     * 上传头像
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/user/account-avatar-update', name: 'app_user_account_avatar_update', methods: ['POST'])]
    public function accountAvatarUpdate(
        Request              $request,
        ObjectStorage        $objectStorage,
        UserRepository       $userRepository,
        Violation            $violation,
        Message              $message,
        #[CurrentUser] ?User $user,
    ): JsonResponse
    {
        /** @var ?UploadedFile $avatar */
        $file = $request->files->get('avatar');
        if (!$file) {
            return $this->acceptWith($violation->withMessages('Update failed'), 417);
        }
        $mimeType = $file->getMimeType();
        $suffixMap = [
            'image/png' => 'png',
            'image/jpg' => 'jpg',
            'image/jpeg' => 'jpg',
            'image/gif' => 'gif',
        ];
        if (!array_key_exists($mimeType, $suffixMap)) {
            return $this->acceptWith($violation->withMessages('Update failed'), 417);
        }
        $fileName = $user->getUserIdentifier();
        $avatar = "account/avatar/$fileName." . $suffixMap[$mimeType];

        $objectStorage->put($avatar, $file->getRealPath(), $mimeType);
        $userRepository->save($user->setAvatar($avatar . '?_t=' . time()), true);

        $message = $message->with('Succeed')->withAnnotation([
            'avatar_url' => $user->getAvatar(),
        ]);
        return $this->acceptWith($message->with('Succeed'));
    }

    /**
     * 解除绑定认证信息
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/user/unbinding-authentication', methods: ['POST'])]
    public function unbindingAuthentication(
        Request                  $request,
        AuthenticationRepository $authRepository,
        Violation                $violation,
        #[CurrentUser] ?User     $user,
    ): JsonResponse
    {
        $id = $request->get('id');
        if (!$id || !is_integer($id)) {
            return $this->acceptWith($violation->withMessages('解绑失败'), 417);
        }
        if ($authRepository->count(['user_id' => $user->getId()]) === 1) {
            return $this->acceptWith($violation->withMessages('至少保留一条认证信息'), 417);
        }
        if (!$auth = $authRepository->findOneBy(['id' => $id, 'user_id' => $user->getId()])) {
            return $this->acceptWith($violation->withMessages('解绑失败'), 417);
        }
        $authRepository->remove($auth, true);
        return $this->json(['message' => 'Succeed']);
    }
}
