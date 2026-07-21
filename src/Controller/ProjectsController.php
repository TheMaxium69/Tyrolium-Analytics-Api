<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Projects;
use App\Repository\ProjectsRepository;

final class ProjectsController extends AbstractController
{
    #[Route('/tag', name: 'app_projects', methods: ['POST'])]
    public function createTag(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        $domain_names = $data['domain_names'];
        $useritium_token = $data['useritium_token'];

        if (!empty($domain_names)) {

            if(!empty($domain_names[0])) {

                // TODO : ajouter le faite de verifier que le token est pas vide
                if (!empty($useritium_token)) {

                    // TODO : ajouter une verification que chaque nom de domaine est pas déjà existant dans la base
                    $projects = $this->findAll();
                    foreach ($projects as $project) {
                        foreach ($project->getDomainNames() as $existingDomain) {
                            if (in_array($existingDomain, $domain_names, true)) {
                                return $this->json([
                                        'status' => 'err',
                                        'message' => 'Domain name already exists'
                                    ]
                                );
                            }
                        }
                    }

                    $clef = uniqid(md5($domain_names[0]));
                    $tag = 'TyroTag-' . $clef;

                    $project = new Projects();
                    $project->setTag($tag);
                    $project->setDomainNames($domain_names);

                    // Requete a useritium-api -> du token -> si c'est good l'id et le groupe/role
                    // conditions si il a le droit de créer un tag
                    $project->setUseritiumId(1); // Temporaire

                    $em->persist($project);
                    $em->flush();

                    return $this->json([
                        'status' => "good",
                        'result' => $project
                    ], 200, [], ['groups' => ['post:tag']]);

                } else {
                    return $this->json([
                        'status' => "err",
                        'result' => 'useritium_token is required'
                    ]);
                }

            } else {
                return $this->json([
                    'status' => "err",
                    'message' => 'domaine invalide'
                ]);
            }
        } else {
            return $this->json([
                'status' => "err",
                'message' => 'no domaine name'
            ]);
        }
    }



    // TODO : afficher toute le project (donc avec ces tag)
    #[Route('/projects/getAll', name: 'app_projects_all', methods: ['GET'])]
    public function getAllTag(Request $request, EntityManagerInterface $em, ProjectsRepository $projectsRepo): Response
    {
        $projects = $projectsRepo->findAll();

        return $this->json([
            'status' => "good",
            'result' => $projects
        ], 200, [], ['groups' => ['post:tag']]);
    }



    // TODO : FINI TON CRUD (Delete, Update ajouter un domaine a un tag)
    #[Route('/projets/delete', name: 'app_projects_delete', methods: ['DELETE'])]
    public function getDeleteProjet(int $id): Response
    {
        $projects = $this->projectRepo->find($id);

        if (!$projects) {
            return $this->json([
                'status' => "err",
                'result' => 'project not found'
            ]);
        }

        $this->projectRepo->remove($projects);
        $this->projectRepo->flush();
    }

}




/*
 *
 *    status -> good / err
 *    message -> ""
 *    result -> le content
 *
 *
 *
 */
