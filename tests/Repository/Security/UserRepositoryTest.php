<?php

namespace Es\CoreBundle\Tests\Repository\Security;

use Es\CoreBundle\Repository\Security\UserRepository;
use Es\CoreBundle\Entity\Security\User;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class UserRepositoryTest extends TestCase
{
    /**
     * @var UserRepositoryCustom
     */
    private $userRepository;

    /** 
     * @var \PHPUnit_Framework_MockObject_MockObject 
     */
    private $managerRegistry;

    protected function setUp(): void
    {
        $this->managerRegistry = $this->getMockBuilder(ManagerRegistry::class)->getMock();
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $classMetadata = $this->getMockBuilder(ClassMetadata::class)->disableOriginalConstructor()->getMock();
        $entityManager->method("getClassMetadata")->will($this->returnValue($classMetadata));
        $this->managerRegistry->method('getManagerForClass')
            ->will($this->returnValue($entityManager));
        $this->userRepository = new UserRepositoryCustom($this->managerRegistry);
    }

    public function testFindUserByUsernameOrEmail()
    {
        $username = "test";
        $result = $this->userRepository->findUserByUsernameOrEmail($username);

        $this->assertInstanceOf(User::class, $result);
    }
}

/**
 * We need to extends the class to write custom function to test our functions
 */
class UserRepositoryCustom extends UserRepository
{
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return new User();
    }
}
