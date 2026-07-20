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
    #[Route('/projects', name: 'app_projects', methods: ['POST'])]
    public function createTag(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        $domain_names = $data['domain_names'];

        if (empty($domain_names)) {
            return $this->json([
                'success' => false,
                'message' => 'No domain names provided'
            ]);
        }

        $domain_good = bin2hex(random_bytes(5));
        $tag = 'TyroTag-' . $domain_good;

        $project = new Projects();
        $project->setTag($tag);
        $project->setDomainNames($domain_names);

        $project->setUseritiumId('useritium_id'); // api useritium requis ?? //

        $em->persist($project);
        $em->flush();

        return $this->json([
            'success' => true,
            'project' => [
                'id' => $project->getId(),
                'tag' => $project->getTag(),
                'domain_names' => $project->getDomainNames(),
            ]
        ]);
    }
}
