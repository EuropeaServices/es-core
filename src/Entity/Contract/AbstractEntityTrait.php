<?php
namespace Es\CoreBundle\Entity\Contract;

use Es\CoreBundle\Entity\Security\User;
use Es\CoreBundle\Entity\Security\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * trait AbstractEntity
 * Must be implement by all entity
 *
 * @since 15/10/2019
 * @author hroux
 */
trait AbstractEntityTrait
{

    /**
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     *
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    protected $createdBy;

    /**
     *
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     * })
     */
    protected $updatedBy;

    /**
     *
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deleted_by", referencedColumnName="id")
     * })
     */
    protected $deletedBy;

    /**
     *
     * @inheritdoc
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     *
     * @inheritdoc
     */
    public function getCreatedAt(): \DateTime
    {
        if (empty($this->createdAt)) {
            $this->setCreatedAt(new \DateTime());
        }
        return $this->createdAt;
    }

    /**
     *
     * @inheritdoc
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     *
     * @inheritdoc
     */
    public function getUpdatedAt(): \DateTime
    {
        if (empty($this->updatedAt)) {
            $this->setUpdatedAt(new \DateTime());
        }
        return $this->updatedAt;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return void
     */
    public function setDeletedAt(\DateTime $deletedAt = null): void
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    /**
     * Set CreatedBy
     *
     * @param UserInterface $createdBy
     *
     * @return void
     */
    public function setCreatedBy(UserInterface $createdBy = null): void
    {
        $this->createdBy = $createdBy;
    }

    /**
     * Get CreatedBy
     *
     * @return UserInterface
     */
    public function getCreatedBy(): UserInterface
    {
        return $this->createdBy;
    }

    /**
     * Set UpdatedBy
     *
     * @param UserInterface $UpdatedBy
     *
     * @return void
     */
    public function setUpdatedBy(UserInterface $updatedBy = null): void
    {
        $this->updatedBy = $updatedBy;
    }

    /**
     * Get UpdatedBy
     *
     * @return UserInterface
     */
    public function getUpdatedBy(): UserInterface
    {
        return $this->updatedBy;
    }

    /**
     * Set deletedBy
     *
     * @param UserInterface $deletedBy
     *
     * @return void
     */
    public function setDeletedBy(UserInterface $deletedBy = null): void
    {
        $this->deletedBy = $deletedBy;
    }

    /**
     * Get DeletedBy
     *
     * @return User
     */
    public function getDeletedBy(): ?UserInterface
    {
        return $this->deletedBy;
    }

    /**
     * 
     * @param string $field
     */
    public function getField(string $field)
    {
        if (property_exists($this, $field)) {
            return $this->$field;
        }
        return null;
    }
}
