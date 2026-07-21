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

        $requiredFields = ['tag_id', 'ip', 'page_name', 'uri', 'isLogin'];      // TODO : verifier bien tout les champs obligatoire
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return $this->json([
                    'status' => 'error',
                    'message' => "Le champ '$field' est obligatoire.",
                    'result' => null
                ]);
            }
        }

        $project = $projectsRepository->find($data['tag_id']);

        if (!$project) {
            return $this->json(['error' => 'Project non trouvé pour ce tag_id.']);
        }

        $input = new Input();
        $input->setTagId($project);

        $input->setIp($data['ip']);
        $input->setPageName($data['page_name']);
        $input->setUri($data['uri']);
        $input->setIsLogin($data['isLogin']);
        $input->setCreatedAt(new \DateTimeImmutable());       // TODO : ajouter la date "CreatedAt"


        $entityManager->persist($input);
        $entityManager->flush();

        return $this->json([                            // TODO : mettre a jour la logique de retour (status, message, result)
            'status' => 'success',                      // TODO : mettre en place le group / ancre
            'message' => 'Input créé avec succès',
            'result' => $input
        ], context: ['groups' => 'input:read']);
    }





    #[Route('/input', name: 'app_input_list', methods: ['GET'])]
    public function index(InputRepository $inputRepository): JsonResponse {

        $inputs = $inputRepository->findAll();

        return $this->json([
            'status' => 'success',
            'message' => 'Liste des inputs récupérée.',
            'result' => $inputs
        ], context: ['groups' => 'input:read']);
    }




    #[Route('/input/project/{id}', name: 'app_input_by_project', methods: ['GET'])]     // TODO : une route read by project (je te donne l'id d'un projet et tu me donne seulement ces vue a lui)
    public function getByProject(int $id, InputRepository $inputRepository, ProjectsRepository $projectsRepository): JsonResponse
    {
        $project = $projectsRepository->find($id);

        if (!$project) {
            return $this->json([
                'status' => 'error',
                'message' => 'Projet introuvable.',
                'result' => null
            ]);
        }

        $inputs = $inputRepository->findBy(['tag' => $project]);

        return $this->json([
            'status' => 'success',
            'message' => 'Inputs du projet récupérés.',
            'result' => $inputs
        ], context: ['groups' => 'input:read']);
    }

    #[Route('/input/search', name: 'app_input_search', methods: ['GET'])]   // TODO : avec l'id de useritium et avec l'ip aussi, et chaque page, uri, ip
    public function search(Request $request, InputRepository $inputRepository): JsonResponse {

        $filters = [
            'projectId' => $request->query->get('project_id'),
            'useritiumId' => $request->query->get('useritium_id'),
            'ip' => $request->query->get('ip'),
            'pageName' => $request->query->get('page_name'),
            'uri' => $request->query->get('uri'),
        ];

        $inputs = $inputRepository->findByAdvancedFilters($filters);

        return $this->json([
            'status' => 'success',
            'message' => count($inputs) > 0 ? 'Résultats trouvés.' : 'Aucun résultat.',
            'result' => $inputs
        ], context: ['groups' => 'input:read']);
    }

}
