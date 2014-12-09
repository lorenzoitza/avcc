<?php

namespace Application\Bundle\FrontBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MediaTypesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MediaTypesRepository extends EntityRepository
{
    public function getAllAsArray(){
        $names = $this->getEntityManager()->createQuery('SELECT mediaTypes.name'
                . ' from ApplicationFrontBundle:MediaTypes mediaTypes'
                )->getScalarResult();
        $mediatypes = array_map("current",$names);
        return $mediatypes;
    }
}
