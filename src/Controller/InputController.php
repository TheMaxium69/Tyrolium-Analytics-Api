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
    public function create(Request $request, EntityManagerInterface $entityManager, ProjectsRepository $projectsRepository, InputRepository $inputRepository): JsonResponse {

        $data = json_decode($request->getContent(), true);

        $project = $data['project'];
        $ip = $data['ip'];
        $page_name = $data['page_name'];
        $uri = $data['uri'];
        $is_login = $data['is_login'];


        if (!isset($data['project']) || !isset($data['ip']) || !isset($data['page_name']) || !isset($data['uri']) || !isset($data['is_login'])) {

            return $this->json([
                'status' => 'error',
                'message' => "A field is required to be filled"
            ]);
        }

        $project = $projectsRepository->findOneBy(['tag' => $project]);

        if (!$project) {

            return $this->json([
                'status' => 'err',
                'message' => 'Project not found for this tag'
            ]);
        }

        // éviter les doubles
        $existingInputs = $inputRepository->findOneBy([
            'project' => $project,
            'ip'         => $ip,
            'page_name'   => $page_name,
            'uri'        => $uri,
            'is_login'    => $is_login
        ]);

        if ($existingInputs) {
            return $this->json([
                'status'  => 'error',
                'message' => 'This input entry already exists.',
                'result'  => $existingInputs
            ]);
        }

        $input = new Input();
        $input->setProject($project);


        $input->setIp($ip);
        $input->setPageName($page_name);
        $input->setUri($uri);
        $input->setIsLogin($is_login);


        $entityManager->persist($input);
        $entityManager->flush();


        return $this->json([                            // TODO : mettre a jour la logique de retour (status, message, result)
            'status' => 'success',                      // TODO : mettre en place le group / ancre
            'message' => 'Input created with success.',
            'result' => $input
        ], 200, [], ['groups' => 'input:read']);
    }





    #[Route('/input', name: 'app_input_list', methods: ['GET'])]
    public function index(InputRepository $inputRepository): JsonResponse {

        $inputs = $inputRepository->findAll();

        return $this->json([
            'status' => 'success',
            'message' => 'Input list founded.',
            'result' => $inputs
        ], 200, [], ['groups' => 'input:read', 'post:tag']);
    }


    #[Route('/input/search', name: 'app_input_search', methods: ['GET'])]                       // TODO : une route read by project (je te donne l'id d'un projet et tu me donne seulement ces vue a lui)
    public function search(Request $request, InputRepository $inputRepository, ProjectsRepository $projectsRepository): JsonResponse {  // TODO : avec l'id de useritium et avec l'ip aussi, et chaque page, uri, ip

        $project = $request->query->get('project');

        if ($project !==null) {
            $project = $projectsRepository->find($project);
            if (!$project) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'No project tag found.',
                    'result' => null
                ]);
            }
        }


        $filters = [
            'project' => $request->query->get('project'),
            'useritiumId' => $request->query->get('useritium_id'),
            'ip' => $request->query->get('ip'),
            'pageName' => $request->query->get('page_name'),
            'uri' => $request->query->get('uri'),
        ];


        $inputs = $inputRepository->findByAdvancedFilters($filters);

        return $this->json([
            'status' => 'success',
            'message' => "Result found.",
            'result' => $inputs
        ], 200, [], ['groups' => 'input:read']);
    }

}





//#[Route('/input/project/{id}', name: 'app_input_by_project', methods: ['GET'])]     // TODO : une route read by project (je te donne l'id d'un projet et tu me donne seulement ces vue a lui)
//    public function getByProject(int $id, InputRepository $inputRepository, ProjectsRepository $projectsRepository): JsonResponse
//{
//    $project = $projectsRepository->find($id);
//
//    if (!$project) {
//        return $this->json([
//            'status' => 'error',
//            'message' => 'Projet introuvable.',
//            'result' => null
//        ]);
//   }
//
//    $inputs = $inputRepository->findBy(['tag' => $project]);
//
//   return $this->json([
//        'status' => 'success',
//        'message' => 'Inputs du projet récupérés.',
//        'result' => $inputs
//    ], 200, [], ['groups' => 'input:read']);
// }
