<?php

namespace Es\CoreBundle\Entity\Security;

use Es\CoreBundle\Entity\Contract\AbstractEntityInterface;
use Es\CoreBundle\Entity\Contract\AbstractEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Es\CoreBundle\Entity\Security\Group;
use Es\CoreBundle\Entity\Security\GroupInterface;
use Es\CoreBundle\Entity\Security\UserInterface;

/**
 * @UniqueEntity(fields="email")
 * @ORM\MappedSuperclass(repositoryClass="Es\CoreBundle\Repository\Security\UserRepository")
 */
class User implements UserInterface, \Serializable, AbstractEntityInterface
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
     * @Assert\NotBlank()
     * @Assert\Length(max=250)
     */
    protected $plainPassword;

    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     *
     * @ORM\Column(type="string", length=64)
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

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    function setPassword($password)
    {
        $this->password = $password;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }
        $roles[] = 'ROLE_USER';

        return $roles;
    }

    function addRole($role)
    {
        $this->roles[] = $role;
    }

    /**
     * @return Collection|GroupInterface[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
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
            $this->groups
            // see section on salt below
            // $this->salt,
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
            $this->groups
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    public function __toString()
    {
        return $this->username;
    }
}
