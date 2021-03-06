<?php

namespace Es\CoreBundle\Repository\Security;

use Es\CoreBundle\Entity\Security\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $class = User::class)
    {
        parent::__construct($registry, $class);
    }

    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if (preg_match('/^.+\@\S+\.\S+$/', $usernameOrEmail)) {
            $user = $this->findOneBy(["email"  => $usernameOrEmail]);
            if (null !== $user) {
                return $user;
            }
        }

        return $this->findOneBy(["username"  => $usernameOrEmail]);
    }

    public function findUserToSendWarningPasswordExpired(
        string $numberPasswordExpired, 
        string $unityDatePasswordExpired,
        string $numberWarningMail, 
        string $unityDateWarningMail)
    {
        $sqlPasswordExpired = "DATE_ADD(su.passwordChangedAt, ".$numberPasswordExpired.",'".strtolower($unityDatePasswordExpired)."')";
        $sqlPasswordExpired = "DATE_SUB(".$sqlPasswordExpired.",  ".$numberWarningMail.",'".strtolower($unityDateWarningMail)."')";
        $qb = $this->createQueryBuilder('su')
        ->andWhere("su.passwordChangedAt IS NOT NULL")
        ->andWhere("(su.mailWarningExpirationPasswordAt < su.passwordChangedAt OR su.mailWarningExpirationPasswordAt IS NULL)")
        ->andWhere($sqlPasswordExpired." < CURRENT_DATE()");

        return $qb->getQuery()->getResult();
    }
}
