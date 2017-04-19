<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
* @ORM\Entity
* @ORM\Table(name="user")
* @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
*/
class User implements UserInterface, \Serializable
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $password;

    //email, recipes, collections, roles,

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $loggedIn;

    /**
     * @ORM\OneToMany(targetEntity="Recipe", mappedBy="author")
     */
    private $recipes;

    /**
     * @ORM\OneToMany(targetEntity="Tag", mappedBy="author")
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="Collection", mappedBy="author")
     */
    private $collections;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->collections = new \Doctrine\Common\Collections\ArrayCollection();
        $this->recipes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
