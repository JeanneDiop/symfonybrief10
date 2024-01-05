<?php

namespace App\Controller;





use App\Entity\Candidature;
use App\Repository\UserRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CandidatureRepository;
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

class CandidatureController extends AbstractController
{
//   lister toutes les candidatures
    #[Route('/api/candidature/list', name: 'ListCandidature', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour modifier une formation')]
    public function index(CandidatureRepository $candidatureRepository, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
       $candidaturelist = $candidatureRepository->findAll();
       $jsonCandidatureList = $serializer->serialize($candidaturelist, 'json', ['groups' => 'getCandidatures']); 
       return new JsonResponse($jsonCandidatureList, Response::HTTP_OK, [], true);
    }
 

    
    // #[Route('/api/candidature/{id}', name: 'showOneCandidature', methods: ['GET'])]
    // public function indexOneCandidature (Candidature $candidature, SerializerInterface $serializer): JsonResponse
    // {
    //     $jsonCandidature = $serializer->serialize($candidature, 'json', ['groups' => 'getCandidatures']);
    //     return new JsonResponse($jsonCandidature, Response::HTTP_OK, [], true);
    // }
    // lister les candidatures refusées
    #[Route('/api/candidaturerefuse', name: 'listCandidaturerefuse', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function listCandidatures(CandidatureRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $candidatures = $repository->findBy(['statut' => 0]);

        $jsonCandidatures = $serializer->serialize($candidatures, 'json', ['groups' => 'getCandidatures']);

        return new JsonResponse($jsonCandidatures, Response::HTTP_OK, [], true);
    }
    // lister les candidatures acceptées
    #[Route('/api/candidatureaccepter', name: 'listCandidatureaccepter', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function listCandidaturesaccepter(CandidatureRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $candidatures = $repository->findBy(['statut' => 1]);

        $jsonCandidatures = $serializer->serialize($candidatures, 'json', ['groups' => 'getCandidatures']);

        return new JsonResponse($jsonCandidatures, Response::HTTP_OK, [], true);
    }
   
// modifier une candidature
    #[Route('/api/candidature/update/{id}', name:"refuseCandidature", methods:['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour modifier une formation')]

    public function RefuseCandidature(Request $request, SerializerInterface $serializer, Candidature $currentCandidature, 
    EntityManagerInterface $entityManager, ): JsonResponse 
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $updatedCandidature = $serializer->deserialize($request->getContent(), 
                Candidature::class, 
                'json', 
                [AbstractNormalizer::OBJECT_TO_POPULATE => $currentCandidature]);
 
        $entityManager->persist($updatedCandidature);
        $entityManager->flush();
        return $this->json(["message"=>" votre candidature a été refusée"]);
   }
// ajouter une acndidature
   
   #[Route('/api/candidature/store', name:"addCandidature", methods: ['POST'])]
    public function addOneCandidature(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager,
    FormationRepository $formationRepository, UserRepository $userRepository, ValidatorInterface $validator, UrlGeneratorInterface $urlGenerator): JsonResponse 
    {
        
        $candidature = $serializer->deserialize($request->getContent(), Candidature::class, 'json');
        
        // On vérifie les erreurs
        $errors = $validator->validate($candidature);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
       

        // On attribue une formation à la candidature qu'on crée 
        $content = $request->toArray();
        
         $formation= $content['formation_id'] ?? -1;
         
         $candidature->setFormation($formationRepository->find($formation));
         
        
        // On attribue un user à la candidature qu'on crée 
        $content = $request->toArray();
        $user_id = $content['user_id'] ?? -1;
        $candidature->setUser($userRepository->find($user_id));
        // dd($formation);
        
        $entityManager->persist($candidature);
        $entityManager->flush();
        return $this->json(["message"=>"candidature ajouté avec succés"]);
        // $jsonCandidature = $serializer->serialize($candidature, 'json', ['groups' => 'getCandidatures']);
        
        // $location = $urlGenerator->generate('addCandidature', ['id' => $candidature->getId()], UrlGeneratorInterface::ABSOLUTE_URL); 

        // return new JsonResponse($jsonCandidature, Response::HTTP_CREATED, ["Location" => $location], true);
       
   }
}
