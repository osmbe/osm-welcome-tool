<?php

namespace App\Controller\App;

use App\Entity\Mapper;
use App\Entity\Note;
use App\Service\RegionsProvider;
use App\Service\TemplatesProvider;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapperController extends AbstractController
{
    public function __construct(
        private RegionsProvider $provider,
        private TemplatesProvider $templatesProvider,
    ) {}

    #[Route('/{regionKey}/mapper/{id}', name: 'app_mapper')]
    public function index(Request $request, string $regionKey, int $id): Response
    {
        $region = $this->provider->getRegion($regionKey);

        /** @var Mapper */
        $mapper = $this->getDoctrine()
            ->getRepository(Mapper::class)
            ->find($id);

        $note = new Note();
        $note->setMapper($mapper);
        /** @todo Set author to logged in user */
        $note->setAuthor($mapper);

        $formNote = $this->createFormBuilder($note)
            ->add('text', TextareaType::class, ['label' => 'Note'])
            ->add('submit', SubmitType::class, ['label' => 'Add'])
            ->getForm();

        $formNote->handleRequest($request);

        if ($formNote->isSubmitted() === true && $formNote->isValid() === true) {
            $note->setDate(new DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($note);
            $entityManager->flush();

            return $this->redirectToRoute('app_mapper', ['regionKey' => $regionKey, 'id' => $id]);
        }

        $templates = $this->templatesProvider->getTemplates();

        return $this->render('app/mapper/index.html.twig', [
            'region' => $region,
            'mapper' => $mapper,
            'changesets' => $mapper->getChangesets(),
            'formNote' => $formNote->createView(),
            'templates' => $templates,
        ]);
    }
}
