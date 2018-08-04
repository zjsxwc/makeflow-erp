<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 30/07/2018
 * Time: 3:29 PM
 */

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @var string
     */
    public $username;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}