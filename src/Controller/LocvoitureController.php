<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Vehicule;
use App\Form\CommandeType;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LocvoitureController extends AbstractController
{
    #[Route('/locvoiture', name: 'app_locvoiture')]
    public function index(VehiculeRepository $repo): Response
    {

        $vehicules = $repo->findAll();
        return $this->render('locvoiture/index.html.twig', [
            'vehicules' => $vehicules,
        ]);
    }
    #[Route('/', name:'home')]
    public function home()
    {
    return $this->render('locvoiture/home.html.twig');
    }

    #[Route('locvoiture/show/{id}', name: 'vehicule_show')]
    public function show($id, VehiculeRepository $repo)
    {
        $vehicule = $repo->find($id);
        
        return $this->render('locvoiture/show.html.twig',[
            'vehicule' => $vehicule,
        ]);
    }


    #[Route("/locvoiture/edit/{id}", name:'vehicule_edit')]
    #[Route('/vehicule/new', name:"vehicule_create")]
    public function form(Request $request, EntityManagerInterface $manager, Vehicule $vehicule = null)
    {   
        if(!$vehicule)
        {
            $vehicule = new Vehicule;
            $vehicule->setCreatedAt(new \DateTime());
        }
        

        $form = $this->createForm(VehiculeType::class, $vehicule);
        
        //dd($request);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() )
               {
                   $vehicule->setCreatedAt(new \DateTime());
                   $manager->persist($vehicule);
                   $manager->flush();
                  return $this->redirectToRoute('home',[
                       'id' => $vehicule->getId()
                   ]);
                }
        return $this->render('locvoiture/form.html.twig', [
            'formVehicule' => $form->createView(),
            'editMode' => $vehicule->getId() !== NULL
        ]);
    }
    #[Route('/locvoiture/delete/{id}', name:"locvoiture_delete")]
    public function delete($id,EntityManagerInterface $manager, VehiculeRepository $repo)
        {
            $vehicule = $repo->find($id);

            $manager->remove($vehicule); //préparé
            $manager->flush(); // exécuter
            
            return $this->redirectToRoute('app_locvoiture'); //redirection
        }
        

    #[Route("/admin/commande/new", name:"admin_new_commande")]
    public function formCommande(Request $request, EntityManagerInterface $manager)
        {
            $commande =new Commande;
            $form = $this->createForm(CommandeType::class,$commande);
            //dd($request);
            $form->handleRequest($request);
            
            if($form->isSubmitted() && $form->isValid())
            {
                $manager->persist($commande); 
                $manager->flush();
                $this->addFlash('success', "Votre commande a bien été enregistré");
    
                return $this->redirectToRoute('vehicule_show');
    
    
            }
            return $this->renderForm('locvoiture/commande.html.twig', [
                'formCommande' => $form
            ]);
    
        }    


}
