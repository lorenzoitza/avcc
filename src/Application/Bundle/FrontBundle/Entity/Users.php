<?php

// src/Application/Bundle/FrontBundle/Entity/Users.php

namespace Application\Bundle\FrontBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="roles", column=@ORM\Column(type="string", name="roles", length=255)),
 *      @ORM\AttributeOverride(name="enabled", column=@ORM\Column( name="enabled", options={"default" = 1})),
 * }) 
 */
class Users extends BaseUser
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \DateTime
     */
    private $created_on;

    /**
     * @var \DateTime
     */
    private $updated_on;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Users
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set created_on
     *
     * @param \DateTime $createdOn
     * @return Users
     */
    public function setCreatedOn($createdOn)
    {
        $this->created_on = $createdOn;

        return $this;
    }

    /**
     * Get created_on
     *
     * @return \DateTime 
     */
    public function getCreatedOn()
    {
        return $this->created_on;
    }

    /**
     * Set updated_on
     *
     * @param \DateTime $updatedOn
     * @return Users
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updated_on = $updatedOn;

        return $this;
    }

    /**
     * Get updated_on
     *
     * @return \DateTime 
     */
    public function getUpdatedOn()
    {
        return $this->updated_on;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedOnValue()
    {
        if(!$this->getCreatedOn())
        {
            $this->created_on = new \DateTime();
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedOnValue()
    {
        $this->updated_on = new \DateTime();
    }

    /**
     * @var \Application\Bundle\FrontBundle\Entity\Organizations
     */
    private $organizations;


    /**
     * Set organizations
     *
     * @param \Application\Bundle\FrontBundle\Entity\Organizations $organizations
     * @return Users
     */
    public function setOrganizations(\Application\Bundle\FrontBundle\Entity\Organizations $organizations = null)
    {
        $this->organizations = $organizations;

        return $this;
    }

    /**
     * Get organizations
     *
     * @return \Application\Bundle\FrontBundle\Entity\Organizations 
     */
    public function getOrganizations()
    {
        return $this->organizations;
    }
    
    public function __toString()
    {
        return $this->getName();
    }
    /**
     * @var \Application\Bundle\FrontBundle\Entity\Users
     */
    private $created_by;

    /**
     * @var \Application\Bundle\FrontBundle\Entity\Users
     */
    private $updated_by;


    /**
     * Set created_by
     *
     * @param \Application\Bundle\FrontBundle\Entity\Users $createdBy
     * @return Users
     */
    public function setCreatedBy(\Application\Bundle\FrontBundle\Entity\Users $createdBy = null)
    {
        $this->created_by = $createdBy;

        return $this;
    }

    /**
     * Get created_by
     *
     * @return \Application\Bundle\FrontBundle\Entity\Users 
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Set updated_by
     *
     * @param \Application\Bundle\FrontBundle\Entity\Users $updatedBy
     * @return Users
     */
    public function setUpdatedBy(\Application\Bundle\FrontBundle\Entity\Users $updatedBy = null)
    {
        $this->updated_by = $updatedBy;

        return $this;
    }

    /**
     * Get updated_by
     *
     * @return \Application\Bundle\FrontBundle\Entity\Users 
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
    }
}
