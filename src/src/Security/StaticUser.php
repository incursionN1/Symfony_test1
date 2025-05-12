<?php
namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class StaticUser implements UserInterface
{
    private $username;
    private $roles;
    private $password;

    public function __construct(string $username, array $roles = [], string $password = 'password')
    {
        $this->username = $username;
        $this->roles = $roles;
        $this->password = $password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function eraseCredentials(): void
    {
        // Ничего не делаем, так как пароль не хранится
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}