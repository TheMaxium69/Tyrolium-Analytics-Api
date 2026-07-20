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

        $input->setIp($data['ip']);
        $input->setPageName($data['page_name']);
        $input->setUri($data['uri']);
        $input->setIsLogin($data['isLogin']);


        $entityManager->persist($input);
        $entityManager->flush();

        return $this->json([
            'message' => 'Input créé avec succès',
            'id' => $input->getId()
        ]);
    }

    #[Route('/input', name: 'app_input_list', methods: ['GET'])]
    public function index(InputRepository $inputRepository): JsonResponse
    {
        $inputs = $inputRepository->findAll();

        $data = array_map(function (Input $input) {
            return [
                'id' => $input->getId(),
                'tag_id' => $input->getTagId() ? $input->getTagId()->getId() : null,
                'ip' => $input->getIp(),
                'page_name' => $input->getPageName(),
                'uri' => $input->getUri(),
                'isLogin' => $input->isLogin(),
            ];
        }, $inputs);

        return $this->json($data);
    }
}
