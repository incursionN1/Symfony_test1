<?php
namespace App\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class StaticUserProvider implements UserProviderInterface
{
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // Здесь вы можете реализовать свою логику проверки пользователя
        // Например, проверять статический список пользователей

        if ($identifier === 'api_user') {
            return new StaticUser('api_user', ['ROLE_API'], '$2y$13$wEtUo.fqCe0fOocn0a8p1.UtCDpns18GzzFn53oBAVa6nuhjypfhi');
        }

        throw new UserNotFoundException();
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof StaticUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return StaticUser::class === $class;
    }
}