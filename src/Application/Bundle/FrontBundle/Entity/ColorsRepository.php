<?php

namespace Application\Bundle\FrontBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ColorsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ColorsRepository extends EntityRepository
{
    public function getAllAsArray(){
        $names = $this->getEntityManager()->createQuery('SELECT colors.name'
                . ' from ApplicationFrontBundle:Colors colors'
                )->getScalarResult();
        $colors = array_map("current",$names);
        return $colors;
    }
}
