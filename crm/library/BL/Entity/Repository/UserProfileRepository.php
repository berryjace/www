<?php

namespace BL\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Collections;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserProfileRepository extends EntityRepository {
    const NOT_FOUND = 1;
    const WRONG_PW = 2;

}

