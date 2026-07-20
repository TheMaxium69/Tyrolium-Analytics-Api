<?php

namespace App\Controller;

use App\Entity\Input;
use App\Repository\InputRepository;
use App\Repository\ProjectsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class InputController extends AbstractController
{
    #[Route('/input', name: 'app_input_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, ProjectsRepository $projectsRepository): JsonResponse {

        $data = json_decode($request->getContent(), true);

        if (!isset($data['tag_id'])) {
            return $this->json(['error' => 'Le champ tag_id est obligatoire.']);
        }

        $project = $projectsRepository->find($data['tag_id']);

        if (!$project) {
            return $this->json(['error' => 'Project non trouvé pour ce tag_id.']);
        }

        $input = new Input();
        $input->setTagId($project);

        // TODO : verifier bien tout les champs obligatoire
        $input->setIp($data['ip']);
        $input->setPageName($data['page_name']);
        $input->setUri($data['uri']);
        $input->setIsLogin($data['isLogin']);

        // TODO : ajouter la date

        $entityManager->persist($input);
        $entityManager->flush();


        // TODO : mettre a jour la logique de retour (status, message, result)
        // TODO : mettre en place le group / ancre
        return $this->json([
            'message' => 'Input créé avec succès',
            'id' => $input->getId()
        ]);
    }

    #[Route('/input', name: 'app_input_list', methods: ['GET'])]
    public function index(InputRepository $inputRepository): JsonResponse
    {
        $inputs = $inputRepository->findAll();


        // en faite vue que vous avez pas de groupe/ancre c chiant en sois

        return $this->json($inputs);
    }


    // TODO : une route read by project (je te donne l'id d'un projet et tu me donne seulement ces vue a lui)

    // TODO : avec l'id de useritium et avec l'ip aussi, et chaque page, uri, ip
}
