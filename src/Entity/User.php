<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(type="string", length=255)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\History", mappedBy="user", orphanRemoval=true)
     */
    private $histories;

    public function __construct(
        int $apiId,
        string $login,
        ?string $email,
        ?string $name,
        ?string $avatar,
        string $provider
    ) {
        $this->login = $login;
        $this->email = $email;
        $this->name = $name;
        $this->avatar = $avatar;
        $this->apiId = $apiId;
        $this->provider = $provider;

        $this->histories = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): ?string
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

    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function updateToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function getUsername(): string
    {
        return $this->getId();
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
