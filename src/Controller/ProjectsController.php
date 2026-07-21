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
    public function createTag(Request $request, EntityManagerInterface $em, projectsRepository $projectsRepo): Response
    {
        $data = json_decode($request->getContent(), true);

        $domain_names = $data['domain_names'];
        $useritium_token = $data['useritium_token'];

        if (!empty($domain_names)) {

            if(!empty($domain_names[0])) {

                if (!empty($useritium_token)) {

                    $projects = $projectsRepo->findAll();
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
    public function getAllProjects(Request $request, EntityManagerInterface $em, ProjectsRepository $projectsRepo): Response
    {
        $projects = $projectsRepo->findAll();

        return $this->json([
            'status' => "good",
            'result' => $projects
        ], 200, [], ['groups' => ['get:project']]);
    }



    // TODO : FINI TON CRUD (Delete)
    #[Route('/projets/delete/{id}', name: 'app_projects_delete', methods: ['DELETE'])]
    public function getDeleteProjet(int $id, EntityManagerInterface $em, ProjectsRepository $projectsRepo): Response
    {
        $projects = $projectsRepo->find($id);

        if (!$projects) {
            return $this->json([
                'status' => "err",
                'result' => 'project not found'
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
    #[Route('/domain/update/{id}', name: 'app_domain_update', methods: ['PUT'])]
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
        $newDomain = $data['domain_names'];

        if (!$newDomain) {
            return $this->json([
                'status' => "err",
                'message' => 'domaine invalide'
            ]);
        }

        $existingDomains = $projects->getDomainNames();
        $domainToAdd = (array) $newDomain;

        $alreadyExists = array_intersect($existingDomains, $domainToAdd);

        if (!empty($alreadyExists)) {
            return $this->json([
                'status' => "err",
                'message' => 'domaine already exists',
                'doublon' => array_values($alreadyExists) // pour affiche les domain en double
            ]);
        }

        $merged = array_merge($existingDomains, $domainToAdd);
        $cleanDomains = array_values(array_unique($merged));



        // J'ai fait ca au début mais j'avais une erreur ca me créer un tableau dans un tableau ligne 175
        // Et quand je l'enlever ca remplacer ce qui avais != ajouter

//        // je vérifie si le domain que j'ajoute n'existe pas deja
//        $domains = $projects->getDomainNames();
//
//        if (in_array($id, $domains, true)) {
//            return $this->json([
//                'status' => "err",
//                'message' => 'domain name already exists'
//            ]);
//        }
//
//        $domains[] = $newDomain;
//
//        $projects->setDomainNames($domains);

        
        $projects->setDomainNames($cleanDomains);
        $em->persist($projects);
        $em->flush();

        return $this->json([
            'status' => "good",
            'result' => $projects
        ], 200, [], ['groups' => ['get:project']]);
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
