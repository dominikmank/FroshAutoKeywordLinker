<?php

namespace Frosh\AutoKeywordLinker\Entity;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class FroshKeywordDefinition extends EntityDefinition
{
    public function getEntityName(): string
    {
        return 'frosh_keywords';
    }

    public function getCollectionClass(): string
    {
        return FroshKeywordsCollection::class;
    }

    public function getEntityClass(): string
    {
        return FroshKeywordsEntity::class;
    }

    public function getDefaults(): array
    {
        return [
            'targetBlank' => false,
            'noFollow' => false
        ];
    }


    protected function defineFields(): FieldCollection
    {
        return (new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new StringField('keyword', 'keyword'))->addFlags(new ApiAware(), new Required()),
            (new JsonField('target_type', 'targetType'))->addFlags(new ApiAware(), new Required()),
            (new BoolField('target_blank', 'targetBlank'))->addFlags(new ApiAware(), new Required()),
            (new BoolField('no_follow', 'noFollow'))->addFlags(new ApiAware(), new Required()),
        ]));
    }
}
