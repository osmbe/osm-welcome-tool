<?php

namespace App\Controller\App;

use App\Entity\Mapper;
use App\Entity\Note;
use App\Entity\Template;
use App\Entity\Welcome;
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
        $entityManager = $this->getDoctrine()->getManager();

        $region = $this->provider->getRegion($regionKey);

        /** @var Mapper */
        $mapper = $this->getDoctrine()
            ->getRepository(Mapper::class)
            ->find($id);

        // Welcome
        if ($request->query->has('welcome') && $request->query->getBoolean('welcome') === true) {
            $welcome = $mapper->getWelcome();
            if (is_null($welcome)) {
                $welcome = new Welcome();
                $welcome->setMapper($mapper);
            }
            $welcome->setDate(new DateTime());

            $entityManager->persist($welcome);
            $entityManager->flush();

            return $this->redirectToRoute('app_mapper', ['regionKey' => $regionKey, 'id' => $id]);
        } elseif ($request->query->has('welcome') && $request->query->getBoolean('welcome') === false) {
            $welcome = $mapper->getWelcome();
            if (!is_null($welcome)) {
                $entityManager->remove($welcome);
                $entityManager->flush();

                return $this->redirectToRoute('app_mapper', ['regionKey' => $regionKey, 'id' => $id]);
            }
        }
        if ($request->query->has('reply') && $request->query->getBoolean('reply') === true) {
            $welcome = $mapper->getWelcome();
            if (is_null($welcome)) {
                $welcome = new Welcome();
                $welcome->setMapper($mapper);
                $welcome->setDate(new DateTime());
            }
            $welcome->setReply(new DateTime());

            $entityManager->persist($welcome);
            $entityManager->flush();

            return $this->redirectToRoute('app_mapper', ['regionKey' => $regionKey, 'id' => $id]);
        } elseif ($request->query->has('reply') && $request->query->getBoolean('reply') === false) {
            $welcome = $mapper->getWelcome();
            if (!is_null($welcome)) {
                $welcome->setReply(null);

                $entityManager->persist($welcome);
                $entityManager->flush();

                return $this->redirectToRoute('app_mapper', ['regionKey' => $regionKey, 'id' => $id]);
            }
        }

        // Templates
        $templates = $this->templatesProvider->getTemplates($regionKey);

        // Get current template based on query
        $templateLocale = $request->query->getAlpha('locale');
        $templateFilename = $request->query->get('template');
        if (!is_null($templateFilename) && $templateLocale !== '') {
            $filter = array_filter($templates, function (Template $template) use ($templateLocale, $templateFilename) {
                return substr($template->getLocale(), 0, 2) === substr($templateLocale, 0, 2) && $template->getFilename() === $templateFilename;
            });
            if (count($filter) > 0) {
                $template = current($filter);
            }
        }
        // Get current template based on mapper locale
        if (!isset($template)) {
            $locale = $mapper->getFirstChangeset()->getLocale();
            if (!is_null($locale)) {
                $filter = array_filter($templates, function (Template $template) use ($locale) {
                    return substr($template->getLocale(), 0, 2) === substr($locale, 0, 2);
                });
                if (count($filter) > 0) {
                    $template = current($filter);
                }
            }
        }
        // Take first template
        if (!isset($template) && count($templates) > 0) {
            $template = current($templates);
        }

        // Notes
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

            $entityManager->persist($note);
            $entityManager->flush();

            return $this->redirectToRoute('app_mapper', ['regionKey' => $regionKey, 'id' => $id]);
        }

        return $this->render('app/mapper/index.html.twig', [
            'region' => $region,
            'mapper' => $mapper,
            'changesets' => $mapper->getChangesets(),
            'formNote' => $formNote->createView(),
            'templates' => $templates,
            'selectedTemplate' => $template ?? null,
        ]);
    }
}
