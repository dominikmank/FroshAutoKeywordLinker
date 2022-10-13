<?php

namespace Frosh\AutoKeywordLinker\Subscriber;

use Frosh\AutoKeywordLinker\Entity\FroshKeywordsEntity;
use Frosh\AutoKeywordLinker\Service\KeywordContentUpdaterService;
use Shopware\Core\Content\Product\Events\ProductIndexerEvent;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductIndexSubscriber implements EventSubscriberInterface
{
    private EntityRepository $froshKeywordsRepository;
    private EntityRepository $productRepository;
    private KeywordContentUpdaterService $contentUpdater;

    public function __construct(EntityRepository $productRepository, EntityRepository $froshKeywordsRepository, KeywordContentUpdaterService $contentUpdater)
    {
        $this->froshKeywordsRepository = $froshKeywordsRepository;
        $this->productRepository = $productRepository;
        $this->contentUpdater = $contentUpdater;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductIndexerEvent::class => 'onProductIndexer',
        ];
    }

    public function onProductIndexer(ProductIndexerEvent $event)
    {
        $ids = $event->getIds();
        if (!count($ids)) {
            return;
        }

        $products = $this->productRepository->search(new Criteria($ids), $event->getContext())->getEntities();

        $iterator = new RepositoryIterator($this->froshKeywordsRepository, $event->getContext(), new Criteria());

        while ($result = $iterator->fetch()) {
            /** @var FroshKeywordsEntity $entity */
            foreach ($result->getEntities() as $entity) {
                /** @var ProductEntity $product */
                foreach ($products as $product) {
                    $description = $product->getDescription();
                    $updatedDescription = $this->contentUpdater->setPlaceholders($entity, $description);

                    if ($description !== $updatedDescription) {
                        $this->productRepository->upsert(
                            [
                                ['id' => $product->getId(), 'description' => $updatedDescription]
                            ],
                            $event->getContext()
                        );
                    }
                }
            }
        }
    }
}
