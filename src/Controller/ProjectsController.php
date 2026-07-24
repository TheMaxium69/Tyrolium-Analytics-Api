<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Projects;
use App\Repository\ProjectsRepository;

final class ProjectsController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProjectsRepository $projectsRepository
    ) {}

    #[Route('/create-project', name: 'create_project', methods: ['POST'])]
    public function createProject(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true); // Parsing objet JSON (JavaScript Object Notation)

        if (!empty($data)) {

            $form_domain_names = $data['domain_names'];
            $form_useritium_token = $data['useritium_token'];

            if (!empty($form_domain_names)) {

                if(!empty($form_domain_names[0])) {

                    if (!empty($form_useritium_token)) {

                        // Requete a useritium-api -> du token -> si c'est good l'id et le groupe/role
                        // conditions si il a le droit de créer un tag
                        $useritium_id = 1; // c le resultat de l'api
                        if ($useritium_id) {

                            // Créer un projet
                            $project = new Projects();

                            // Je met l'utilisateur
                            $project->setUseritiumId($useritium_id);

                            // Domaine
                            $projectAll = $this->projectsRepository->findAll();
                            foreach ($projectAll as $projectOne) {

                                $domainAll = $projectOne->getDomainNames();
                                foreach ($domainAll as $domainOne) {

                                    if (in_array($domainOne, $form_domain_names, true)) {
                                        return $this->json([
                                                'status' => 'err',
                                                'message' => 'Domain name already exists'
                                            ]
                                        );
                                    }

                                }
                            }

                            $project->setDomainNames($form_domain_names);

                            // Tag
                            $clef = uniqid(md5($form_domain_names[0]));
                            $tag = 'TyroTag-' . $clef;
                            $project->setTag($tag);

                            $this->entityManager->persist($project);
                            $this->entityManager->flush();

                            return $this->json([
                                'status' => "good",
                                'message' => 'project created',
                                'result' => $project
                            ], 200, [], ['groups' => ['post:project']]); // verbe:entity

                        } else {
                            return $this->json([
                                'status' => "err",
                                'result' => 'useritium_token is invalide'
                            ]);
                        }
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
        } else {
            return $this->json([
                'status' => "err",
                'message' => 'json invalide'
            ]);
        }
    }



    // TODO : afficher toute le project (donc avec ces tag)
    #[Route('/get-project-all', name: 'get_project_all', methods: ['GET'])]
    public function getProjectAll(ProjectsRepository $projectsRepo): Response
    {
        $projects = $projectsRepo->findAll();

        return $this->json([
            'status' => "good",
            'message' => "all projects",
            'result' => $projects
        ], 200, [], ['groups' => ['get:project']]);
    }

    // getOne
    #[Route('/get-projects-one/{id}', name: 'get_projects_one', methods: ['GET'])]
    public function getProjectOne(Projects $project): Response
    {
        return $this->json([
            'status' => 'good',
            'message' => 'Project found',
            'result' => $project
        ], 200, [], ['groups' => ['get:project']]);
    }

    // TODO : FINI TON CRUD (Delete)
    #[Route('/delete-projet/{id}', name: 'delete_project', methods: ['DELETE'])]
    public function getDeleteProjet(int $id, EntityManagerInterface $em, ProjectsRepository $projectsRepo): Response
    {
        $projects = $projectsRepo->find($id);

        if (!$projects) {
            return $this->json([
                'status' => "err",
                'message' => 'project not found'
            ]);
        }

        $em->remove($projects);
        $em->flush();

        return $this->json([
            'status' => "good",
            'message' => 'project has been deleted'
        ]);
    }



    // TODO : FINI TON CRUD (Update ajouter un domaine a un tag)
    #[Route('/update-project/{id}', name: 'update_project', methods: ['PUT'])]
    public function getUpdateDomain(int $id, EntityManagerInterface $em, ProjectsRepository $projectsRepo, Request $request,): Response
    {
        $projects = $projectsRepo->find($id);
        if (!$projects) {
            return $this->json([
                'status' => "err",
                'message' => 'project not found'
            ]);
        }

        $data = json_decode($request->getContent(), true);
        $new_domain = $data['domain_names'];

        if (!$new_domain) {
            return $this->json([
                'status' => "err",
                'message' => 'domaine invalide'
            ]);
        }

        // TODO : verifi est-ce que le domain existe dans le projet actuel ou dans un autre projet
        $all_projects = $projectsRepo->findAll();

        foreach ($all_projects as $project) {
            $domain_names = $project->getDomainNames();

            if (in_array($new_domain, $domain_names, true)) {
                if ($project->getId() == $projects->getId()) {
                    return $this->json([
                        'status' => "err",
                        'message' => 'domaine already exists in this project'
                    ]);
                }
                return $this->json([
                    'status' => "err",
                    'message' => 'domaine already exists in other project'
                ]);
            }
        }

        $existing_domains = $projects->getDomainNames();
        $existing_domains[] = $new_domain;

        $projects->setDomainNames($existing_domains);

        $em->persist($projects);
        $em->flush();

        return $this->json([
            'status' => "good",
            "message" => "domain added to project",
            'result' => $projects
        ], 200, [], ['groups' => ['get:project']]);
    }

}

/*
 *
 * CRUD -> ENTITY (Projet)
 *
 * Create -> Entity (Projet) pour créer
 * Read One -> Entity (Projet) By ID (ID)
 * Read All -> ENtity (Projet) Tous ALL
 * Update -> Entity (Projet) pour edit l'entity
 * Delete -> Entity (Projet) pour supprimer le projet et donc le tag
 *
 *
 */


/*
 *
 *    status -> good / err
 *    message -> ""
 *    result -> le content
 *
 *
 *
 */
