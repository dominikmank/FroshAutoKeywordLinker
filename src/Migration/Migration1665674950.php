<?php declare(strict_types=1);

namespace Frosh\AutoKeywordLinker\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1665674950 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1665674950;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(
            <<<SQL
CREATE TABLE `frosh_keywords` (
    `id` BINARY(16) NOT NULL,
    `keyword` VARCHAR(255) NOT NULL,
    `target_type` JSON NOT NULL,
    `target_blank` TINYINT(1) NOT NULL DEFAULT '0',
    `no_follow` TINYINT(1) NOT NULL DEFAULT '0',
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `json.frosh_keywords.target_type` CHECK (JSON_VALID(`target_type`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
