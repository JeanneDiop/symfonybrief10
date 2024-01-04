<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationController extends AbstractController
{
    #[Route('/api/formation/list', name: 'ListFormation', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(FormationRepository $formationRepository, SerializerInterface $serializer): JsonResponse
    {
       $formationlist = $formationRepository->findAll();
       $jsonFormationList = $serializer->serialize($formationlist, 'json', ['groups' => 'getFormations']); 
       return new JsonResponse($jsonFormationList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/formation/{id}', name: 'showOneFormation', methods: ['GET'])]
    public function indexOneFormation(Formation $formation, SerializerInterface $serializer): JsonResponse
    {
        $jsonFormation = $serializer->serialize($formation, 'json', ['groups' => 'getFormations']);
        return new JsonResponse($jsonFormation, Response::HTTP_OK, [], true);
    }
   
    #[Route('/api/formation/store', name:"addFormation", methods: ['POST'])]
     #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour publier une formation')]

    public function addOneFormation(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator ): JsonResponse 
    {
      
      $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $formation = $serializer->deserialize($request->getContent(), Formation::class, 'json');
        
          // On vérifie les erreurs
          $errors = $validator->validate($formation);

          if ($errors->count() > 0) { 
              return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
          }
         
  
        $entityManager->persist($formation);
        $entityManager->flush();

        $jsonFormation = $serializer->serialize($formation, 'json', ['groups' => 'getFormations']);
        
        $location = $urlGenerator->generate('addFormation', ['id' => $formation->getId()], UrlGeneratorInterface::ABSOLUTE_URL); 

        return new JsonResponse($jsonFormation, Response::HTTP_CREATED, ["Location" => $location], true);
   }

//modifier une formation
   #[Route('/api/formation/update/{id}', name:"updateFormation", methods:['PUT'])]
   #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour modifier une formation')]
   public function updateOneFormation(Request $request, SerializerInterface $serializer, Formation $currentFormation, EntityManagerInterface $entityManager)
   {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
       $updatedFormation = $serializer->deserialize($request->getContent(), 
               Formation::class, 
               'json', 
               [AbstractNormalizer::OBJECT_TO_POPULATE => $currentFormation]);
               
       
       $entityManager->persist($updatedFormation);
       $entityManager->flush();
       return $this->json(["message"=>"modifier avec succés"]);
  }

  /**Supprime une formation */
  #[Route('/api/formation/delete/{id}', name: 'deleteFormation', methods: ['DELETE'])]
  #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour modifier une formation')]
  public function deleteOneFormation(Formation $formation, EntityManagerInterface $entityManager) // le param coverter envoie directement la donnée dont nous avons besoin avec l'instanciation de l'entité Book et sa variable $book grâce à l'id précisé dans la route 
  {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
      $entityManager->remove($formation);
      $entityManager->flush();
    // return new JsonResponse(null, Response::HTTP_NO_CONTENT); // code 204 car il est correct et qu'il n'ya rien à retourner 
    return $this->json(["message"=>"supprimer avec succés"]);
  }

}