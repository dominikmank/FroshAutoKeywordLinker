<?php declare(strict_types=1);

namespace Frosh\AutoKeywordLinker\Entity;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(FroshKeywordsEntity $entity)
 * @method void                set(string $key, FroshKeywordsEntity $entity)
 * @method FroshKeywordsEntity[]    getIterator()
 * @method FroshKeywordsEntity[]    getElements()
 * @method FroshKeywordsEntity|null get(string $key)
 * @method FroshKeywordsEntity|null first()
 * @method FroshKeywordsEntity|null last()
 */
class FroshKeywordsCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return FroshKeywordsEntity::class;
    }
}
