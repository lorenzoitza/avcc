<?php

namespace Application\Bundle\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AcidDetectionStrips
 *
 * @ORM\Table(name="acid_detection_strips")
 * @ORM\Entity
 */
class AcidDetectionStrips
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     * @Assert\NotBlank(message="Color name is required")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Organizations", cascade={"all","merge","persist","refresh","remove"}, fetch="EAGER", inversedBy="acidDetectionStripOrg")
     * @ORM\JoinColumn(
     *     name="organization_id",
     *     referencedColumnName="id",
     *     nullable=false,
     *     onDelete="CASCADE"
     * )
     * @var integer 
     */
    private $organization;
    
    /**
     * Returns acid deduction strip
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get Id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return \Application\Bundle\FrontBundle\Entity\AcidDeductionStrips
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    
    /**
     * Set organization.
     *
     * @param \Application\Bundle\FrontBundle\Entity\Organizations $organization
     *
     * @return \Application\Bundle\FrontBundle\Entity\AcidDetectionStrip
     */
    public function setOrganization(\Application\Bundle\FrontBundle\Entity\Organizations $organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get formate media type
     * 
     * @return \Application\Bundle\FrontBundle\Entity\Organizations
     */
    public function getOrganization()
    {
        return $this->organization;
    }
}
