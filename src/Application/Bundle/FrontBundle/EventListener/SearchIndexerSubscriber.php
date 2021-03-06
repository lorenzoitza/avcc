<?php
/**
 * AVCC
 * 
 * @category AVCC
 * @package  Application
 * @author   Nouman Tayyab <nouman@weareavp.com>
 * @author   Rimsha Khalid <rimsha@weareavp.com>
 * @license  AGPLv3 http://www.gnu.org/licenses/agpl-3.0.txt
 * @copyright Audio Visual Preservation Solutions, Inc
 * @link     http://avcc.weareavp.com
 */
namespace Application\Bundle\FrontBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
//use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Application\Bundle\FrontBundle\Entity\AudioRecords;
use Application\Bundle\FrontBundle\Entity\FilmRecords;
use Application\Bundle\FrontBundle\Entity\VideoRecords;
use Application\Bundle\FrontBundle\SphinxSearch\SphinxSearch;

class SearchIndexerSubscriber implements EventSubscriber
{

    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
        );
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->index($args, 'update');
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index($args, 'insert');
    }

    public function index(LifecycleEventArgs $args, $type)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();
        $recordTypeId = null;

        if ($entity instanceof AudioRecords) {
            $recordTypeId = 1;
        } elseif ($entity instanceof VideoRecords) {
            $recordTypeId = 3;
        } elseif ($entity instanceof FilmRecords) {
            $recordTypeId = 2;
        }
        if ($recordTypeId) {
            $sphinxSearch = new SphinxSearch($entityManager, $entity->getId(), $recordTypeId);
            if ($type === 'insert')
                $sphinxSearch->insert();
            else
                $sphinxSearch->update();
        }
    }

}
