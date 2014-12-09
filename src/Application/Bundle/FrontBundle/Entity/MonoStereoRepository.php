<?php

namespace Application\Bundle\FrontBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MonoStereoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MonoStereoRepository extends EntityRepository
{
    public function getAllAsArray(){
        $names = $this->getEntityManager()->createQuery('SELECT monoStereo.name'
                . ' from ApplicationFrontBundle:MonoStereo monoStereo'
                )->getScalarResult();
        $monoStero = array_map("current",$names);
        return $monoStero;
    }
}
