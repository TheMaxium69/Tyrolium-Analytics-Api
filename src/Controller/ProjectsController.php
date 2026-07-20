<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Projects;

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

                // TODO : ajouter une verification que chaque nom de domaine est pas déjà existant dans la base

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


    // TODO : FINI TON CRUD (Delete, Update ajouter un domaine a un tag)


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
