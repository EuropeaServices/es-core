<?php

namespace Es\CoreBundle\Entity\Security;

use Es\CoreBundle\Entity\Contract\AbstractEntityInterface;
use Es\CoreBundle\Entity\Contract\AbstractEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Es\CoreBundle\Entity\Security\GroupInterface;
use Es\CoreBundle\Entity\Security\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @UniqueEntity(fields="email")
 * @ORM\MappedSuperclass(repositoryClass="Es\CoreBundle\Repository\Security\UserRepository")
 */
class User   implements UserInterface, \Serializable, AbstractEntityInterface, PasswordAuthenticatedUserInterface
{

    use AbstractEntityTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    protected $username;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    protected $lastname;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $email;

    /**
     * @Assert\Length(max=250)
     */
    protected $plainPassword;

    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    /**
     * @ORM\Column(name="confirmation_token", type="string", length=255, nullable=true)
     */
    protected $confirmationToken;

    /**
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
     */
    protected $passwordRequestedAt;

    /**
     * @ORM\Column(name="password_changed_at", type="datetime", nullable=true)
     */
    protected $passwordChangedAt;

    /**
     * @ORM\Column(name="mail_warning_expiration_password_at", type="datetime", nullable=true)
     */
    protected $mailWarningExpirationPasswordAt;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\Column(name="roles", type="array")
     */
    protected $roles = array();

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     * @ORM\JoinTable(name="lnk_user_group",
     *   joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     *   }
     * )
     */
    protected $groups;

    public function __construct()
    {
        $this->isActive = true;
        $this->groups = new ArrayCollection();
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }
        $roles[] = 'ROLE_USER';

        return $roles;
    }

    function addRole(string $role): self
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * @return Collection|GroupInterface[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function addGroup(GroupInterface $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    public function removeGroup(GroupInterface $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
        }

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->firstname,
            $this->lastname,
            $this->email,
            $this->password,
            $this->isActive,
            $this->groups,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->firstname,
            $this->lastname,
            $this->email,
            $this->password,
            $this->isActive,
            $this->groups,
        ) = unserialize($serialized);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationToken(): ?\DateTime
    {
        return $this->confirmationToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationToken(?\DateTime $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPasswordRequestedAt(\DateTime $date = null): self
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt(): ?\DateTime
    {
        return $this->passwordRequestedAt;
    }

    public function __toString(): string
    {
        return $this->username;
    }

    /**
     * Get the value of passwordChangedAt
     */
    public function getPasswordChangedAt(): ?\DateTime
    {
        return $this->passwordChangedAt;
    }

    /**
     * Set the value of passwordChangedAt
     *
     * @return  self
     */
    public function setPasswordChangedAt(?\DateTime $passwordChangedAt): self
    {
        $this->passwordChangedAt = $passwordChangedAt;

        return $this;
    }

    /**
     * Get the value of mailWarningExpirationPasswordAt
     */
    public function getMailWarningExpirationPasswordAt(): ?\DateTime
    {
        return $this->mailWarningExpirationPasswordAt;
    }

    /**
     * Set the value of mailWarningExpirationPasswordAt
     *
     * @return  self
     */
    public function setMailWarningExpirationPasswordAt(?\DateTime $mailWarningExpirationPasswordAt): self
    {
        $this->mailWarningExpirationPasswordAt = $mailWarningExpirationPasswordAt;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getFieldsList(): array
    {
        return [
            'lastname',
            'firstname',
            'email', 
            'username',
            'groups',
        ];
    }
}
