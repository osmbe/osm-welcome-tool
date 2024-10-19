<?php

namespace App\Controller\App;

use App\Entity\Mapper;
use App\Entity\Note;
use App\Entity\Region;
use App\Entity\Template;
use App\Entity\User;
use App\Entity\Welcome;
use App\Service\RegionsProvider;
use App\Service\TemplatesProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MapperController extends AbstractController
{
    private Mapper $mapper;

    private ?User $user;

    /** @var Template[] */
    private array $templates;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RegionsProvider $provider,
        private readonly TemplatesProvider $templatesProvider,
    ) {
    }

    #[Route('/{regionKey}/mapper/{id}', name: 'app_mapper', requirements: ['regionKey' => '[\w\-_]+'])]
    #[Route('/{continent}/{regionKey}/mapper/{id}', name: 'app_mapper_full', requirements: ['continent' => 'asia|africa|australia|europe|north-america|south-america', 'regionKey' => '[\w\-_]+'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, string $regionKey, int $id, ?string $continent): Response
    {
        $region = $this->provider->getRegion($continent, $regionKey);

        $this->user = $this->getUser(); // @phpstan-ignore assign.propertyType
        $this->mapper = $this->entityManager->find(Mapper::class, $id);

        $mapperRegions = array_map(fn (Region $region) => $region->getId(), $this->mapper->getRegion()->toArray());

        if (!\in_array($regionKey, $mapperRegions, true)) {
            throw $this->createNotFoundException('This mapper was not detected in this region.');
        }

        // Welcome
        if ($request->query->has('welcome')) {
            $this->updateWelcomeDate($request->query->getBoolean('welcome'));

            return $this->redirectToRoute('app_mapper_full', ['continent' => $continent, 'regionKey' => $regionKey, 'id' => $this->mapper->getId()]);
        }
        if ($request->query->has('reply')) {
            $this->updateWelcomeReply($request->query->getBoolean('reply'));

            return $this->redirectToRoute('app_mapper_full', ['continent' => $continent, 'regionKey' => $regionKey, 'id' => $this->mapper->getId()]);
        }

        // Templates
        $this->templates = $this->templatesProvider->getTemplates($region['key']);
        $template = $this->getDefaultTemplate($request);

        // Notes
        $formNote = $this->note($request);
        if (true === $formNote->isSubmitted() && true === $formNote->isValid()) {
            return $this->redirectToRoute('app_mapper_full', ['continent' => $continent, 'regionKey' => $regionKey, 'id' => $this->mapper->getId()]);
        }

        // Prev/Next mapper
        /** @var Mapper[] */
        $mappers = $this->provider->getEntity($regionKey)?->getMappers()->toArray() ?? [];

        $firstChangetsetCreatedAt = array_map(fn (Mapper $mapper): ?\DateTimeImmutable => $mapper->getFirstChangeset()->getCreatedAt(), $mappers);
        array_multisort($firstChangetsetCreatedAt, \SORT_DESC, $mappers);

        $current = array_search($this->mapper, $mappers, true);
        $prev = $current > 0 ? $mappers[$current - 1] : null;
        $next = $current < (\count($mappers) - 1) ? $mappers[$current + 1] : null;

        return $this->render('app/mapper/index.html.twig', [
            'region' => $region,
            'mapper' => $this->mapper,
            'prev_mapper' => $prev,
            'next_mapper' => $next,
            'changesets' => $this->mapper->getChangesets(),
            'formNote' => $formNote->createView(),
            'templates' => $this->templates,
            'selectedTemplate' => $template,
        ]);
    }

    private function updateWelcomeDate(bool $state): void
    {
        $welcome = $this->mapper->getWelcome();
        if (null === $welcome) {
            $welcome = new Welcome();
            $welcome->setMapper($this->mapper);
        }

        if (true === $state) {
            $welcome->setDate(new \DateTime());
            $welcome->setUser($this->user);

            $this->entityManager->persist($welcome);
        } else {
            $this->entityManager->remove($welcome);
        }

        $this->entityManager->flush();
    }

    private function updateWelcomeReply(bool $state): void
    {
        $welcome = $this->mapper->getWelcome();
        if (null === $welcome) {
            $welcome = new Welcome();
            $welcome->setMapper($this->mapper);
            $welcome->setDate(new \DateTime());
            $welcome->setUser($this->user);
        }
        $welcome->setReply($state ? new \DateTime() : null);

        $this->entityManager->persist($welcome);
        $this->entityManager->flush();
    }

    private function getDefaultTemplate(Request $request): ?Template
    {
        // Get current template based on query
        $templateLocale = $request->query->getAlpha('locale');
        $templateFilename = $request->query->get('template');
        if (null !== $templateFilename && '' !== $templateLocale) {
            $filter = array_filter(
                $this->templates,
                fn (Template $template): bool => substr($template->getLocale(), 0, 2) === substr($templateLocale, 0, 2)
                    && $template->getFilename() === $templateFilename
            );
            if (\count($filter) > 0) {
                return current($filter);
            }
        }

        // Get current template based on mapper locale
        $locale = $this->mapper->getFirstChangeset()->getLocale();
        if (null !== $locale) {
            $filter = array_filter($this->templates, fn (Template $template): bool => substr($template->getLocale(), 0, 2) === substr($locale, 0, 2));
            if (\count($filter) > 0) {
                return current($filter);
            }
        }

        // Take first template
        if (\count($this->templates) > 0) {
            return current($this->templates);
        }

        return null;
    }

    private function note(Request $request): FormInterface
    {
        $note = new Note();
        $note->setMapper($this->mapper);
        $note->setAuthor($this->user);

        $form = $this->createFormBuilder($note)
            ->add('text', TextareaType::class, ['label' => 'Note'])
            ->add('submit', SubmitType::class, ['label' => 'Add'])
            ->getForm();

        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $note->setDate(new \DateTime());

            $this->entityManager->persist($note);
            $this->entityManager->flush();
        }

        return $form;
    }
}
