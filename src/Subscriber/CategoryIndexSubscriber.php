<?php

namespace Frosh\AutoKeywordLinker\Subscriber;

use Frosh\AutoKeywordLinker\Entity\FroshKeywordsEntity;
use Frosh\AutoKeywordLinker\Service\KeywordContentUpdaterService;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Category\Event\CategoryIndexerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CategoryIndexSubscriber implements EventSubscriberInterface
{
    private EntityRepository $categoryRepository;
    private EntityRepository $froshKeywordsRepository;
    private KeywordContentUpdaterService $contentUpdater;

    public function __construct(EntityRepository $categoryRepository, EntityRepository $froshKeywordsRepository, KeywordContentUpdaterService $contentUpdater)
    {
        $this->categoryRepository = $categoryRepository;
        $this->froshKeywordsRepository = $froshKeywordsRepository;
        $this->contentUpdater = $contentUpdater;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CategoryIndexerEvent::class => 'onCategoryIndex'
        ];
    }

    public function onCategoryIndex(CategoryIndexerEvent $event): void
    {
        $ids = $event->getIds();
        if (!count($ids)) {
            return;
        }

        $categories = $this->categoryRepository->search(new Criteria($ids), $event->getContext())->getEntities();

        $iterator = new RepositoryIterator($this->froshKeywordsRepository, $event->getContext(), new Criteria());

        while ($result = $iterator->fetch()) {
            /** @var FroshKeywordsEntity $entity */
            foreach ($result->getEntities() as $entity) {

                /** @var CategoryEntity $category */
                foreach ($categories as $category) {
                    $description = $category->getDescription();

                    if (!$description) {
                        continue;
                    }

                    $updatedDescription = $this->contentUpdater->setPlaceholders($entity, $description);

                    if ($description !== $updatedDescription) {

                        $this->categoryRepository->upsert(
                            [
                                ['id' => $category->getId(), 'description' => $updatedDescription]
                            ],
                            $event->getContext()
                        );
                    }
                }
            }
        }
    }
}
