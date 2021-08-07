<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210807123034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_A1696B18B9CA839A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__changeset AS SELECT id, mapper_id, editor, comment, changes_count, extent, created_at, locale FROM changeset');
        $this->addSql('DROP TABLE changeset');
        $this->addSql('CREATE TABLE changeset (id INTEGER NOT NULL, mapper_id INTEGER NOT NULL, editor VARCHAR(255) DEFAULT NULL COLLATE BINARY, comment VARCHAR(255) DEFAULT NULL COLLATE BINARY, extent CLOB NOT NULL COLLATE BINARY --(DC2Type:array)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , locale VARCHAR(255) DEFAULT NULL COLLATE BINARY, create_count INTEGER NOT NULL, reasons CLOB DEFAULT NULL --(DC2Type:array)
        , modify_count INTEGER NOT NULL, delete_count INTEGER NOT NULL, harmful BOOLEAN DEFAULT NULL, suspect BOOLEAN DEFAULT NULL, checked BOOLEAN DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_A1696B18B9CA839A FOREIGN KEY (mapper_id) REFERENCES mapper (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO changeset (id, mapper_id, editor, comment, create_count, extent, created_at, locale) SELECT id, mapper_id, editor, comment, changes_count, extent, created_at, locale FROM __temp__changeset');
        $this->addSql('DROP TABLE __temp__changeset');
        $this->addSql('CREATE INDEX IDX_A1696B18B9CA839A ON changeset (mapper_id)');
        $this->addSql('DROP INDEX UNIQ_38B5A4E4D109C490');
        $this->addSql('CREATE TEMPORARY TABLE __temp__mapper AS SELECT id, welcome_id, changesets_count, account_created, region, display_name, status, image FROM mapper');
        $this->addSql('DROP TABLE mapper');
        $this->addSql('CREATE TABLE mapper (id INTEGER NOT NULL, welcome_id INTEGER DEFAULT NULL, changesets_count INTEGER NOT NULL, account_created DATETIME NOT NULL, region VARCHAR(255) NOT NULL COLLATE BINARY, display_name VARCHAR(255) NOT NULL COLLATE BINARY, status VARCHAR(255) NOT NULL COLLATE BINARY, image VARCHAR(255) DEFAULT NULL COLLATE BINARY, PRIMARY KEY(id), CONSTRAINT FK_38B5A4E4D109C490 FOREIGN KEY (welcome_id) REFERENCES welcome (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO mapper (id, welcome_id, changesets_count, account_created, region, display_name, status, image) SELECT id, welcome_id, changesets_count, account_created, region, display_name, status, image FROM __temp__mapper');
        $this->addSql('DROP TABLE __temp__mapper');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38B5A4E4D109C490 ON mapper (welcome_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_A1696B18B9CA839A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__changeset AS SELECT id, mapper_id, editor, comment, extent, created_at, locale FROM changeset');
        $this->addSql('DROP TABLE changeset');
        $this->addSql('CREATE TABLE changeset (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, mapper_id INTEGER NOT NULL, editor VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, extent CLOB NOT NULL --(DC2Type:array)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , locale VARCHAR(255) DEFAULT NULL, tags CLOB NOT NULL COLLATE BINARY --(DC2Type:array)
        , changes_count INTEGER NOT NULL)');
        $this->addSql('INSERT INTO changeset (id, mapper_id, editor, comment, extent, created_at, locale) SELECT id, mapper_id, editor, comment, extent, created_at, locale FROM __temp__changeset');
        $this->addSql('DROP TABLE __temp__changeset');
        $this->addSql('CREATE INDEX IDX_A1696B18B9CA839A ON changeset (mapper_id)');
        $this->addSql('DROP INDEX UNIQ_38B5A4E4D109C490');
        $this->addSql('CREATE TEMPORARY TABLE __temp__mapper AS SELECT id, welcome_id, region, display_name, account_created, changesets_count, status, image FROM mapper');
        $this->addSql('DROP TABLE mapper');
        $this->addSql('CREATE TABLE mapper (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, welcome_id INTEGER DEFAULT NULL, region VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, account_created DATETIME NOT NULL, changesets_count INTEGER NOT NULL, status VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO mapper (id, welcome_id, region, display_name, account_created, changesets_count, status, image) SELECT id, welcome_id, region, display_name, account_created, changesets_count, status, image FROM __temp__mapper');
        $this->addSql('DROP TABLE __temp__mapper');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38B5A4E4D109C490 ON mapper (welcome_id)');
    }
}
