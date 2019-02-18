<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="integer", name="api_id")
     */
    private $apiId;

    /**
     * @ORM\Column(type="string")
     */
    private $provider;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $accessToken;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $roles = 'ROLE_USER';

    public function __construct(
        string $username,
        string $email,
        string $name,
        string $avatar,
        string $provider,
        int $apiId
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->name = $name;
        $this->avatar = $avatar;
        $this->apiId = $apiId;
        $this->provider = $provider;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getAvatar(): string
    {
        return $this->avatar;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getApiId(): int
    {
        return $this->apiId;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function updateToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getRoles(): array
    {
        return explode(',', $this->roles);
    }

    public function getPassword(): void
    {
        return;
    }

    public function getSalt(): void
    {
        return;
    }

    public function eraseCredentials(): void
    {
        return;
    }
}
