<?php

namespace Frosh\AutoKeywordLinker\Subscriber;

use Frosh\AutoKeywordLinker\Entity\FroshKeywordsEntity;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Category\Event\CategoryIndexerEvent;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandler;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CategoryIndexSubscriber implements EventSubscriberInterface
{
    private EntityRepository $categoryRepository;
    private EntityRepository $froshKeywordsRepository;

    public function __construct(EntityRepository $categoryRepository, EntityRepository $froshKeywordsRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->froshKeywordsRepository = $froshKeywordsRepository;
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
                $keyword = $entity->getKeyword();
                $linkHref = $entity->getTargetType();
                /** @var CategoryEntity $category */
                foreach ($categories as $category) {
                    $description = $category->getDescription();
                    if (!$description) {
                        continue;
                    }

                    $document = new \DOMDocument();
                    $document->loadHTML($description, LIBXML_HTML_NOIMPLIED + LIBXML_HTML_NODEFDTD + LIBXML_NOERROR);
                    foreach ($document->getElementsByTagName('a') as $href) {
                        if ($href->getAttribute('data-seolink') && $href->parentNode !== null) {
                            $linkText = $document->createTextNode($href->textContent);
                            $href->parentNode->insertBefore($linkText, $href);
                            $href->parentNode->removeChild($href);
                        }
                    }
                    $description = $document->saveHTML();

                    if (strpos($description, $keyword) !== false) {
                        $link = SeoUrlPlaceholderHandler::DOMAIN_PLACEHOLDER . '/';
                        switch ($linkHref['entity']) {
                            case 'product':
                                $link .= 'product.detail/' . $linkHref['id'] . '#';
                                break;
                            case 'category':
                                $link .= 'navigation/' . $linkHref['id'] . '#';
                                break;
                            case 'absolute_url':
                                $link = $linkHref['url'];
                                break;
                        }

                        $description = str_replace(
                            $keyword,
                            "<a href=\"$link\" data-seolink=\"true\">" . $keyword . '</a>',
                            $description
                        );

                        $this->categoryRepository->upsert(
                            [['id' => $category->getId(), 'description' => $description]],
                            $event->getContext()
                        );
                    }
                }
            }
        }

        dd($categories);
    }
}
