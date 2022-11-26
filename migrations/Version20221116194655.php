<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221116194655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migrate to multiple regions per user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE mapper_region (mapper_id INTEGER NOT NULL, region_id VARCHAR(255) NOT NULL, PRIMARY KEY(mapper_id, region_id), CONSTRAINT FK_6E8B9023B9CA839A FOREIGN KEY (mapper_id) REFERENCES mapper (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6E8B902398260155 FOREIGN KEY (region_id) REFERENCES region (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6E8B9023B9CA839A ON mapper_region (mapper_id)');
        $this->addSql('CREATE INDEX IDX_6E8B902398260155 ON mapper_region (region_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__mapper AS SELECT id, region, display_name, account_created, changesets_count, status, image FROM mapper');
        $this->addSql('DROP TABLE mapper');
        $this->addSql('CREATE TABLE mapper (id INTEGER NOT NULL, display_name VARCHAR(255) NOT NULL, account_created DATETIME NOT NULL, changesets_count INTEGER NOT NULL, status VARCHAR(255) NOT NULL, image CLOB DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO mapper (id, display_name, account_created, changesets_count, status, image) SELECT id, display_name, account_created, changesets_count, status, image FROM __temp__mapper');
        $this->addSql('INSERT INTO mapper_region (mapper_id, region_id) SELECT id, region FROM __temp__mapper');
        $this->addSql('DROP TABLE __temp__mapper');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__mapper_region AS SELECT mapper_id, region_id FROM mapper_region');
        $this->addSql('DROP TABLE mapper_region');
        $this->addSql('CREATE TEMPORARY TABLE __temp__mapper AS SELECT id, display_name, account_created, changesets_count, status, image FROM mapper');
        $this->addSql('DROP TABLE mapper');
        $this->addSql('CREATE TABLE mapper (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, display_name VARCHAR(255) NOT NULL, account_created DATETIME NOT NULL, changesets_count INTEGER NOT NULL, status VARCHAR(255) NOT NULL, image CLOB DEFAULT NULL, region VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO mapper (id, display_name, account_created, changesets_count, status, image, region) SELECT m.id, m.display_name, m.account_created, m.changesets_count, m.status, m.image, mr.region_id FROM __temp__mapper m LEFT JOIN (SELECT mapper_id, region_id FROM __temp__mapper_region mr GROUP BY 1) mr ON m.id = mr.mapper_id');
        $this->addSql('DROP TABLE __temp__mapper');
        $this->addSql('DROP TABLE __temp__mapper_region');
    }
}
