<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Entity\Commande;
use App\Entity\Vehicule;
use App\Form\CommandeType;
use App\Form\VehiculeType;
use PHPUnit\TextUI\Command;
use App\Repository\MembreRepository;
use App\Repository\CommandeRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/admin/vehicules', name:"admin_vehicules")]
    public function adminVehicule(VehiculeRepository $repo, EntityManagerInterface $manager)
    {
        $colonnes = $manager->getClassMetadata(Vehicule::class)->getFieldNames();

        $vehicules =$repo->findAll();

        return $this->render('admin/admin_vehicules.html.twig', [
            'vehicules' => $vehicules,
            'colonnes' => $colonnes
        ]);
    }
    #[Route("/admin/vehicule/edit/{id}", name:"admin_edit_vehicule")]
    #[Route("/admin/vehicule/new", name:"admin_new_vehicule")]
    public function formVehicule(Request $globals, EntityManagerInterface $manager, Vehicule $vehicule = null)
    {
            if($vehicule == null)
        {

        $vehicule = new Vehicule;
        $vehicule->setCreatedAt(new \DateTime);  

        }
        $form = $this->createForm(VehiculeType::class, $vehicule);      
        $form->handleRequest($globals);

        if($form->isSubmitted() && $form->isValid())
        {
            
            $manager->persist($vehicule); 
            $manager->flush();
            $this->addFlash('success', "Le vehicule a bien été enregistré");
            // permet de crée un message qui sera affiché une fois à l'utilisateur
            //arg 1 : type du message (tout ce qu'on veut)
            //arg 2 contenu du message

            return $this->redirectToRoute('admin_vehicules');
        }

        return $this->renderForm('admin/form_ad.html.twig', [
            'formVehicule' => $form,
            //'editMode' => $vehicule->getId() !== null
            
        ]); 
        }   

        
    #[Route('/admin/vehicule/delete/{id}', name:"admin_vehicule_delete")]
    public function delete($id,EntityManagerInterface $manager, VehiculeRepository $repo)
    {
            $vehicule = $repo->find($id);

            $manager->remove($vehicule); //préparé
            $manager->flush(); // exécuter
            $this->addFlash('success', "L'article a bien été suppremé");
            return $this->redirectToRoute('admin_vehicules'); //redirection
    }
    
    #[Route('/admin/membres', name:"admin_membres")]
    public function adminMembre(MembreRepository $repo, EntityManagerInterface $manager)
    {
        $colonnes = $manager->getClassMetadata(Membre::class)->getFieldNames();

        $membres =$repo->findAll();

        return $this->render('admin/gestion_membre.html.twig', [
            'membres' => $membres,
            'colonnes' => $colonnes
        ]);
    }


    #[Route('/admin/gestion_commande', name:"admin_Commande")]
    public function adminCommande(CommandeRepository $repo, EntityManagerInterface $manager)
    {
        $colonnes = $manager->getClassMetadata(Commande::class)->getFieldNames();

        $Commandes =$repo->findAll();

        return $this->render('admin/gestion_commande.html.twig', [
            'commandes' => $Commandes,
            'colonnes' => $colonnes
        ]);
    
    }

  



        
    
}
