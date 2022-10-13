<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class DataImportController extends AbstractController
{
    #[Route('/dataimportuser', name: 'app_data_importuser')]
    public function index(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response
    {

        $usersjson = file_get_contents('./visiteurjson.json');

        $users = json_decode($usersjson);
        //var_dump($users);
        foreach ($users as $user) {
            $newUser = new User(); //j'instancie un objet de la classe User
            $newUser->setLogin($user->login); //je lui affecte la valeur 'elisabeth2' à son attribut Login
            $newUser->setOldId($user->id);
            $newUser->setNom($user->nom); //je lui affecte la valeur 'Windsor' à son attribut Nom
            $newUser->setPrenom($user->prenom); //je lui affecte la valeur 'Elisabeth' à son attribut Prenom
            $newUser->setCp($user->cp); //je lui affecte la valeur '74000' à son attribut cp
            $newUser->setVille($user->ville); //je lui affecte la valeur 'London' à son attribut Ville
            //pour affecter une date à l'attribut DateEmbauche, on instancie un nouvel objet DateTime
            //auquel on passe la date au format 'YYYY-MM-DD'
            $newUser->setDateEmbauche(new \DateTime($user->dateEmbauche));
            $newUser->setAdresse($user->adresse);
            $plaintextpassword = $user->mdp; //on stocke le mot de passe en clair dans une variable
            $hashedpassword = $passwordHasher->hashPassword($newUser, $plaintextpassword); //on hache le mot de passe
            //grace à la méthode hashPassword()
            $newUser->setPassword($hashedpassword); //j'affecte le mot de passe haché à l'attribut Password de mon objet

            //Faire persister l'objet créé = l'enregistrer en base de données gràce à l'ORM Doctrine
            $doctrine->getManager()->persist($newUser); //je fais persister l'objet $newUser en base de données
            $doctrine->getManager()->flush(); //flush est à appeler après avoir fait un persist

        }
        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }

    #[Route('/dataimportfichefrais', name: 'app_data_importfichefrais')]
    public function importFicheFrais(ManagerRegistry $doctrine): Response
    {
        $fichefraisjson = file_get_contents('./fichefrais.json');
        $fichefrais = json_decode($fichefraisjson);
        foreach ($fichefrais as $fichefrai) {
            $newFicheFrais = new FicheFrais();
            $newFicheFrais->setMois($fichefrai->mois);
            $newFicheFrais->setNbJustificatifs($fichefrai->nbJustificatifs);
            $newFicheFrais->setMontantValid($fichefrai->montantValide);
            $newFicheFrais->setDateModif(new \DateTime($fichefrai->dateModif));
            $user=$doctrine->getRepository(User::class)->findBy(['oldId'=>$fichefrai->idVisiteur]);
            $newFicheFrais->setUser($user);
            switch ($fichefrai->etat){
                case 'RB':
                   $etat=$doctrine->getRepository(Etat::class)->find(3);


                case 'VA':
                    $etat=$doctrine->getRepository(Etat::class)->find(4);

                case 'CL':
                    $etat=$doctrine->getRepository(Etat::class)->find(1);

                case 'CR':
                    $etat=$doctrine->getRepository(Etat::class)->find(2);
            }
                $newFicheFrais->setEtat($etat);

             //$doctrine->getManager()->persist($newFicheFrais); //je fais persister l'objet $newUser en base de données
             // $doctrine->getManager()->flush(); //flush est à appeler après avoir fait un persist
        }

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);

    }
}



