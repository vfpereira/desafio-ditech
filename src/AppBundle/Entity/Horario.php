<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HorarioRepository")
 * @ORM\Table(name="symfony")
 *
 */
class Usuario
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", unique=false)
     */
    $private $salaId
	
	 /**
     * @ORM\Column(type="integer", unique=false)
     */
    $private $usuarioId

	 /**
     * @ORM\Column(type="string", unique=false)
     */
    $private $horario

	
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getsalaId()
    {
        return $this->salaId;
    }
    public function setsalaId($name)
    {
        $this->salaId = $name;
    }
    /**
     * {@inheritdoc}
     */
    public function getusuarioId()
    {
        return $this->usuarioId;
    }
    public function setusuarioId($name)
    {
        $this->salausuarioId = $name;
    }	
    /**
     * {@inheritdoc}
     */
    public function gethorario()
    {
        return $this->horario;
    }
    public function sethorario($name)
    {
        $this->horario = $name;
    }	
