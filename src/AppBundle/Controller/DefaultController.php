<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Usuario;
use AppBundle\Entity\Sala;
use AppBundle\Entity\Horario;
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
				$this->add($usuario);
				return $this->render('default/index.html.twig', ['msg' =>"Cadastro efetuado com sucesso. Para continuar faça o login"]	);
			}				
		}		
	
		if($this->checkUser($request->request->get('email'),$request->request->get('password')))
		{
			$user = $this->getDoctrine()
			             ->getRepository('AppBundle:Usuario')
					     ->findAll();
			$email = $this->getDoctrine()
						  ->getRepository('AppBundle:Usuario')
						  ->findOneByEmail($request->request->get('email'));			 
			
			return $this->render('default/admin.html.twig',['user' => $user, 'admin' =>$email]);	
		}
		else if ($request->request->get('email'))
		{
			return $this->render('default/index.html.twig', ['msg' =>"Usuario ou senha invalida"]	);
		}
				
        return $this->render('default/index.html.twig', ['msg' => '']);
    }
	
	/**
     * @Route("/admin/users/{adminId}", name="adminUsers", defaults={"adminId"=0})
    */
	public function adminUsers(Request $request,$adminId)
	{  
	    $user = $this->getDoctrine()
		             ->getRepository('AppBundle:Usuario')
				     ->findAll();
		
		$admin = $this->getDoctrine()
		    		 ->getRepository('AppBundle:Usuario')
					 ->findOneById($adminId);
		
		return $this->render('default/admin.html.twig',['user' => $user,'admin' =>$admin]);
	}
	
	
	/**
     * @Route("/admin/usersDetail/{id}/{adminId}", name="adminUsersDetail" , defaults={"id" = 0,"adminId"=0})
    */
	public function adminUsersDetail(Request $request,$id,$adminId)
	{
		$user = $this->getDoctrine()
					 ->getRepository('AppBundle:Usuario')
					 ->findOneById($id);
		$admin = $this->getDoctrine()
					 ->getRepository('AppBundle:Usuario')
					 ->findOneById($adminId);	
	
		return $this->render('default/adminUserDetail.html.twig',['user' => $user,'admin' => $admin]);
	}
	
	/**
     * @Route("/admin/usersEdit/{id}/{adminId}", name="adminUsersEdit" , defaults={"id" = 0,"adminId"=0})
    */
	public function adminUsersEdit(Request $request,$id,$adminId)
	{   
		
		if ($request->request->get('edit'))
		{
			$this->updateUser($id,
			   			      $request->request->get('name'),
						      $request->request->get('lastname'),
						      $request->request->get('email'));
		}	
	
		
		$user = $this->getDoctrine()
					 ->getRepository('AppBundle:Usuario')
					 ->findOneById($id);
					 
		$admin = $this->getDoctrine()
					 ->getRepository('AppBundle:Usuario')
					 ->findOneById($adminId);
		
		return $this->render('default/adminUserEdit.html.twig',['user' => $user,'admin'=>$admin]);
	}
	
	/**
     * @Route("/admin/usersDelete/{id}/{adminId}", name="adminUsersDelete" , defaults={"id" = 0,"adminId"=0})
    */	
	public function adminUsersDelete(Request $request,$id,$adminId)
	{   
		$horario = $this->getDoctrine()
					 ->getRepository('AppBundle:Horario')
					 ->findByusuarioId($id);
		
		foreach ($horario as $item)
			$this->deletes($item);
			
		$user = $this->getDoctrine()
					 ->getRepository('AppBundle:Usuario')
					 ->findOneById($id);
		
		$this->deletes($user);
		
		$user = $this->getDoctrine()
		             ->getRepository('AppBundle:Usuario')
				     ->findAll();
		
		$admin = $this->getDoctrine()
		    		 ->getRepository('AppBundle:Usuario')
					 ->findOneById($adminId);
		
		if ($id==$adminId)
			return $this->render('default/index.html.twig', ['msg' =>""]	);
		else
			return $this->render('default/admin.html.twig',['user' => $user,'admin' =>$admin]);
	}
	
		/**
     * @Route("/admin/salas/{adminId}", name="adminSalas", defaults={"adminId"=0})
    */
	public function adminSalas(Request $request,$adminId)
	{  
	    $user = $this->getDoctrine()
		             ->getRepository('AppBundle:Sala')
				     ->findAll();
		
		$admin = $this->getDoctrine()
		    		 ->getRepository('AppBundle:Usuario')
					 ->findOneById($adminId);
		
		return $this->render('default/adminSalas.html.twig',['user' => $user,'admin' =>$admin]);
	}
	
		/**
     * @Route("/admin/salaAdd/{adminId}", name="adminSalaAdd" , defaults={"adminId"=0})
    */
	public function adminSalaAdd(Request $request,$adminId)
	{   
		if ($request->request->get('add'))
		{	
			$sala=new sala($request->request->get('name'));
			$this->add($sala);
		}
		$admin = $this->getDoctrine()
				 ->getRepository('AppBundle:Usuario')
				 ->findOneById($adminId);
			
		return $this->render('default/adminAddSala.html.twig',['admin'=>$admin]);
	}
	
	/**
     * @Route("/admin/salaDetail/{id}/{adminId}", name="adminSalaDetail" , defaults={"id" = 0,"adminId"=0})
    */
	public function adminSalaDetail(Request $request,$id,$adminId)
	{
		$sala = $this->getDoctrine()
					 ->getRepository('AppBundle:Sala')
					 ->findOneById($id);
		$admin =$this->getDoctrine()
					 ->getRepository('AppBundle:Usuario')
					 ->findOneById($adminId);
		$horarios = $this->getDoctrine()
		             ->getRepository('AppBundle:Horario')
				     ->findAll();
		
		//var_dump($horarios);die;
		
		for ($i=0;$i<=23;$i++)			 
		{
			$horario[$i]['horario']=$i;					
			$horario[$i]['reservado']='Sem reserva';
			$horario[$i]['autor'] = '';
			$horario[$i]['autorId'] = 0;
			$horario[$i]['outrasala']=0;
			$query    = $this->getDoctrine()
							 ->getRepository('AppBundle:Horario')
							 ->findBy((array('usuarioId' => $adminId,'horario'=>$i)));
			if (count($query)==1)
			{
				$horario[$i]['outrasala']=1;
			}
			
		}
	
		foreach ($horarios as $item)
		{
			$horario[$item->getHorario()]['reservado']='reservado';
			
		    $autor =$this->getDoctrine()
						 ->getRepository('AppBundle:Usuario')
					     ->findOneById($item->getUsuarioId());
						 
			$horario[$item->getHorario()]['autor']= $autor->getName();
			$horario[$item->getHorario()]['autorId'] = $item->getUsuarioId()	;	 
			
      	}

	
		return $this->render('default/adminSalaDetail.html.twig',['user' => $sala,'admin' => $admin,'horarios'=>$horario]);
	}
	
	/**
    * @Route("/admin/salaEdit/{id}/{adminId}", name="adminSalaEdit" , defaults={"id" = 0,"adminId"=0})
    */
	public function adminSalaEdit(Request $request,$id,$adminId)
	{   
		
		if ($request->request->get('edit'))
			$this->updateSala($id,$request->request->get('name'));
			
		
		$sala = $this->getDoctrine()
					 ->getRepository('AppBundle:Sala')
					 ->findOneById($id);
					 
		$admin = $this->getDoctrine()
					 ->getRepository('AppBundle:Usuario')
					 ->findOneById($adminId);
		
		$horarios = $this->getDoctrine()
		             ->getRepository('AppBundle:Horario')
				     ->findBysalaId($id);
		
		for ($i=0;$i<=23;$i++)			 
		{
			$horario[$i]['horario']=$i;					
			$horario[$i]['reservado']='Sem reserva';
			$horario[$i]['autor'] = '';
			$horario[$i]['autorId'] = 0;
			$horario[$i]['outrasala']=0;
			$query    = $this->getDoctrine()
							 ->getRepository('AppBundle:Horario')
							 ->findBy((array('usuarioId' => $adminId,'horario'=>$i)));
			if (count($query)==1)
			{
				$horario[$i]['outrasala']=1;
			}
			
		}
	
		foreach ($horarios as $item)
		{
			$horario[$item->getHorario()]['reservado']='reservado';
			
		    $autor =$this->getDoctrine()
						 ->getRepository('AppBundle:Usuario')
					     ->findOneById($item->getUsuarioId());
						 
			$horario[$item->getHorario()]['autor']= $autor->getName();
			$horario[$item->getHorario()]['autorId'] = $item->getUsuarioId()	;	 
			
      	}
		
		
		return $this->render('default/adminSalaEdit.html.twig',['user' => $sala,'admin'=>$admin,'horarios'=>$horario]);
	}
	
	
		/**
     * @Route("/admin/salaDelete/{id}/{adminId}", name="adminSalaDelete" , defaults={"id" = 0,"adminId"=0})
    */	
	public function adminSalaDelete(Request $request,$id,$adminId)
	{   
	
		$horario = $this->getDoctrine()
					 ->getRepository('AppBundle:Horario')
					 ->findBySalaId($id);
		
		foreach ($horario as $item)
			$this->deletes($item);
		
		$sala = $this->getDoctrine()
					 ->getRepository('AppBundle:Sala')
					 ->findOneById($id);
		
		$this->deletes($sala);
		
		$sala = $this->getDoctrine()
		             ->getRepository('AppBundle:Sala')
				     ->findAll();
		
		$admin = $this->getDoctrine()
		    		 ->getRepository('AppBundle:Usuario')
					 ->findOneById($adminId);
		
		return $this->render('default/adminSalas.html.twig',['user' => $sala,'admin' =>$admin]);
	}
	
	/**
    * @Route("/admin/salaReserva/{id}/{adminId}/{horarioId}", name="adminSalaReserva" , defaults={"id" = 0,"adminId"=0, "horarioId"=0})
    */
	public function adminSalaReserva(Request $request,$id,$adminId,$horarioId)
	{   
	
		$reserva = new horario($id,$adminId,$horarioId);
	
		$this->add($reserva);
		
		$sala = $this->getDoctrine()
					 ->getRepository('AppBundle:Sala')
					 ->findOneById($id);
					 
		$admin = $this->getDoctrine()
					 ->getRepository('AppBundle:Usuario')
					 ->findOneById($adminId);
					 
		$horarios = $this->getDoctrine()
		             ->getRepository('AppBundle:Horario')
				     ->findAll();
		
		for ($i=0;$i<=23;$i++)			 
		{
			$horario[$i]['horario']=$i;					
			$horario[$i]['reservado']='Sem reserva';
			$horario[$i]['autor'] = '';
			$horario[$i]['autorId'] = 0;
			$horario[$i]['outrasala']=0;
			$query    = $this->getDoctrine()
							 ->getRepository('AppBundle:Horario')
							 ->findBy((array('usuarioId' => $adminId,'horario'=>$i)));
			if (count($query)==1)
			{
				$horario[$i]['outrasala']=1;
			}
			
		}
	
		foreach ($horarios as $item)
		{
			$horario[$item->getHorario()]['reservado']='reservado';
			
		    $autor =$this->getDoctrine()
						 ->getRepository('AppBundle:Usuario')
					     ->findOneById($item->getUsuarioId());
						 
			$horario[$item->getHorario()]['autor']= $autor->getName();
			$horario[$item->getHorario()]['autorId'] = $item->getUsuarioId()	;	 
			
      	}

		return $this->render('default/adminSalaEdit.html.twig',['user' => $sala,'admin'=>$admin,'horarios'=>$horario]);
	}
	
	
	/**
    * @Route("/admin/salaReserva/delete/{id}/{adminId}/{horarioId}", name="adminSalaReservaDelete" , defaults={"id" = 0,"adminId"=0, "horarioId"=0})
    */
	public function adminSalaReservaDelete(Request $request,$id,$adminId,$horarioId)
	{   
	
		//$reserva = new horario($id,$adminId,$horarioId);
;
		$reserva = $this->getDoctrine()
						->getRepository('AppBundle:Horario')
						->findOneBy(array('usuarioId' => $adminId, 'salaId' => $id,'horario'=>$horarioId));
	
		$this->deletes($reserva);
		
		$sala = $this->getDoctrine()
					 ->getRepository('AppBundle:Sala')
					 ->findOneById($id);
					 
		$admin = $this->getDoctrine()
					 ->getRepository('AppBundle:Usuario')
					 ->findOneById($adminId);
					 
		$horarios = $this->getDoctrine()
		             ->getRepository('AppBundle:Horario')
				     ->findAll();
		
		for ($i=0;$i<=23;$i++)			 
		{
			$horario[$i]['horario']=$i;					
			$horario[$i]['reservado']='Sem reserva';
			$horario[$i]['autor'] = '';
			$horario[$i]['autorId'] = 0;
			$horario[$i]['outrasala']=0;
			$query    = $this->getDoctrine()
							 ->getRepository('AppBundle:Horario')
							 ->findBy((array('usuarioId' => $adminId,'horario'=>$i)));
			if (count($query)==1)
			{
				$horario[$i]['outrasala']=1;
			}
			
		}
	
		foreach ($horarios as $item)
		{
			$horario[$item->getHorario()]['reservado']='reservado';
			
		    $autor =$this->getDoctrine()
						 ->getRepository('AppBundle:Usuario')
					     ->findOneById($item->getUsuarioId());
						 
			$horario[$item->getHorario()]['autor']= $autor->getName();
			$horario[$item->getHorario()]['autorId'] = $item->getUsuarioId()	;	 
			
      	}			 
					 
		return $this->render('default/adminSalaEdit.html.twig',['user' => $sala,'admin'=>$admin,'horarios'=>$horario]);
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
	
	
	function deletes($usuario)
	{
		$em = $this->getDoctrine()->getManager();

		$em->remove($usuario);

		$em->flush();

		return true;
	}
	
	
	function add($usuario)
	{
		$em = $this->getDoctrine()->getManager();

		$em->persist($usuario);

		$em->flush();

		return true;
	}
	
	function updateUser($userId,$name,$lastName,$email)
	{
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('AppBundle:Usuario')->find($userId);

		if (!$user) 
		{
			throw $this->createNotFoundException('No user found for id '.$userId);
		}

		$user->setName($name);
		$user->setLastName($lastName);
		$user->setEmail($email);
		$em->flush();

	}
	
	function updateSala($userId,$name)
	{
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('AppBundle:Sala')->find($userId);

		if (!$user) 
		{
			throw $this->createNotFoundException('No sala found for id '.$userId);
		}

		$user->setName($name);
		$em->flush();

	}
	
	
	function checkUser($email,$password)
	{
		$user = $this->getDoctrine()
        ->getRepository('AppBundle:Usuario')
        ->findOneByEmail($email);
		//var_dump($user);die;
		if ($user && $user->getEmail()==$email && $user->getPassword()==$password ) 
			return true;
	    else
			return false;
	}
	
}
