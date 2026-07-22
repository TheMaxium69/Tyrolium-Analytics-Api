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

        $tag_id = $data['tag_id'];
        $ip = $data['ip'];
        $page_name = $data['page_name'];
        $uri = $data['uri'];
        $isLogin = $data['isLogin'];

        if (!$tag_id || !$ip || !$page_name || !$uri || !$isLogin) {
            return $this->json([
                'status' => 'error',
                'message' => "Un champ obligatoire n'est pas remplie."
            ]);
        }

        $project = $projectsRepository->find($tag_id);

        if (!$project) {
            return $this->json([
                'status' => 'err',
                'message' => 'Project non trouvé pour ce tag.'
            ]);
        }

        $input = new Input();
        $input->setTagId($project);

        $input->setIp($ip);
        $input->setPageName($page_name);
        $input->setUri($uri);
        $input->setIsLogin($isLogin);


        $entityManager->persist($input);
        $entityManager->flush();

        return $this->json([                            // TODO : mettre a jour la logique de retour (status, message, result)
            'status' => 'success',                      // TODO : mettre en place le group / ancre
            'message' => 'Input créé avec succès',
            'result' => $input
        ], 200, [], ['groups' => 'input:read']);
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
            'message' => "Résultat trouvés.",
            'result' => $inputs
        ], context: ['groups' => 'input:read']);
    }

}
