<?php

namespace App\Controller\App;

use App\Entity\Mapper;
use App\Entity\Note;
use App\Entity\Template;
use App\Entity\Welcome;
use App\Service\RegionsProvider;
use App\Service\TemplatesProvider;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapperController extends AbstractController
{
    private ObjectManager $entityManager;
    private Mapper $mapper;
    /** @var Template[] */
    private array $templates;

    public function __construct(
        private RegionsProvider $provider,
        private TemplatesProvider $templatesProvider,
    ) {
    }

    #[Route('/{regionKey}/mapper/{id}', name: 'app_mapper')]
    public function index(Request $request, string $regionKey, Mapper $mapper): Response
    {
        $this->entityManager = $this->getDoctrine()->getManager();
        $this->mapper = $mapper;

        $region = $this->provider->getRegion($regionKey);

        // Welcome
        if ($request->query->has('welcome')) {
            $this->updateWelcomeDate($request->query->getBoolean('welcome'));

            return $this->redirectToRoute('app_mapper', ['regionKey' => $regionKey, 'id' => $this->mapper->getId()]);
        }
        if ($request->query->has('reply')) {
            $this->updateWelcomeReply($request->query->getBoolean('reply'));

            return $this->redirectToRoute('app_mapper', ['regionKey' => $regionKey, 'id' => $this->mapper->getId()]);
        }

        // Templates
        $this->templates = $this->templatesProvider->getTemplates($regionKey);
        $template = $this->getDefaultTemplate($request);

        // Notes
        $formNote = $this->note($request);
        if ($formNote->isSubmitted() === true && $formNote->isValid() === true) {
            return $this->redirectToRoute('app_mapper', ['regionKey' => $regionKey, 'id' => $this->mapper->getId()]);
        }

        return $this->render('app/mapper/index.html.twig', [
            'region' => $region,
            'mapper' => $this->mapper,
            'changesets' => $this->mapper->getChangesets(),
            'formNote' => $formNote->createView(),
            'templates' => $this->templates,
            'selectedTemplate' => $template,
        ]);
    }

    private function updateWelcomeDate(bool $state): void
    {
        $welcome = $this->mapper->getWelcome();
        if (is_null($welcome)) {
            $welcome = new Welcome();
            $welcome->setMapper($this->mapper);
        }

        if ($state === true) {
            $welcome->setDate(new DateTime());
            $this->entityManager->persist($welcome);
        } else {
            $this->entityManager->remove($welcome);
        }

        $this->entityManager->flush();
    }

    private function updateWelcomeReply(bool $state): void
    {
        $welcome = $this->mapper->getWelcome();
        if (is_null($welcome)) {
            $welcome = new Welcome();
            $welcome->setMapper($this->mapper);
            $welcome->setDate(new DateTime());
        }
        $welcome->setReply($state ? new DateTime() : null);

        $this->entityManager->persist($welcome);
        $this->entityManager->flush();
    }

    private function getDefaultTemplate(Request $request): Template | null
    {
        // Get current template based on query
        $templateLocale = $request->query->getAlpha('locale');
        $templateFilename = $request->query->get('template');
        if (!is_null($templateFilename) && $templateLocale !== '') {
            $filter = array_filter($this->templates, function (Template $template) use ($templateLocale, $templateFilename) {
                return substr($template->getLocale(), 0, 2) === substr($templateLocale, 0, 2) && $template->getFilename() === $templateFilename;
            });
            if (count($filter) > 0) {
                return current($filter);
            }
        }

        // Get current template based on mapper locale
        $locale = $this->mapper->getFirstChangeset()->getLocale();
        if (!is_null($locale)) {
            $filter = array_filter($this->templates, function (Template $template) use ($locale) {
                return substr($template->getLocale(), 0, 2) === substr($locale, 0, 2);
            });
            if (count($filter) > 0) {
                return current($filter);
            }
        }

        // Take first template
        if (count($this->templates) > 0) {
            return current($this->templates);
        }

        return null;
    }

    private function note(Request $request): FormInterface
    {
        $note = new Note();
        $note->setMapper($this->mapper);
        /** @todo Set author to logged in user */
        $note->setAuthor($this->mapper);

        $form = $this->createFormBuilder($note)
            ->add('text', TextareaType::class, ['label' => 'Note'])
            ->add('submit', SubmitType::class, ['label' => 'Add'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $note->setDate(new DateTime());

            $this->entityManager->persist($note);
            $this->entityManager->flush();
        }

        return $form;
    }
}
