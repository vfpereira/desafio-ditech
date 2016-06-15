<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Usuario;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {   
		if ($request->request->get('_nome'))
		{
			$usuario=new usuario($request->request->get('_nome'),
								 $request->request->get('_sobrenome'),
								 $request->request->get('_email'),
								 $request->request->get('_password'));	

			if(!$this->seekUserbyEmail($usuario->getEmail()))
			{
				$this->addUser($usuario);
				return $this->render('default/index.html.twig', ['msg' =>"Cadastro efetuado com sucesso. Para continuar faça o login"]	);
			}				
		}				
		
        return $this->render('default/index.html.twig', ['msg' => '']);
    }
	
	/**
     * @Route("/admin/users", name="adminUsers")
    */
	public function adminUsers(Request $request)
	{
		return $this->render('default/admin.html.twig');
	}
	
	function seekUserbyEmail($email)
	{
		$email = $this->getDoctrine()
        ->getRepository('AppBundle:Usuario')
        ->findByEmail($email);

		if (!$email) 
			return false;
	    else
			return true;
	}
	
	function addUser($usuario)
	{
		$em = $this->getDoctrine()->getManager();

		$em->persist($usuario);

		$em->flush();

		return true;
	}
	
}
