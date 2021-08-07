<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210807132310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE note (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, mapper_id INTEGER NOT NULL, author_id INTEGER NOT NULL, date DATETIME NOT NULL, text CLOB NOT NULL)');
        $this->addSql('CREATE INDEX IDX_CFBDFA14B9CA839A ON note (mapper_id)');
        $this->addSql('CREATE INDEX IDX_CFBDFA14F675F31B ON note (author_id)');
        $this->addSql('DROP INDEX IDX_A1696B18B9CA839A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__changeset AS SELECT id, mapper_id, editor, comment, extent, created_at, locale, reasons, harmful, suspect, checked, create_count, modify_count, delete_count, changes_count FROM changeset');
        $this->addSql('DROP TABLE changeset');
        $this->addSql('CREATE TABLE changeset (id INTEGER NOT NULL, mapper_id INTEGER NOT NULL, editor VARCHAR(255) DEFAULT NULL COLLATE BINARY, comment VARCHAR(255) DEFAULT NULL COLLATE BINARY, extent CLOB NOT NULL COLLATE BINARY --(DC2Type:array)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , locale VARCHAR(255) DEFAULT NULL COLLATE BINARY, reasons CLOB DEFAULT NULL COLLATE BINARY --(DC2Type:array)
        , harmful BOOLEAN DEFAULT NULL, suspect BOOLEAN DEFAULT NULL, checked BOOLEAN DEFAULT NULL, create_count INTEGER DEFAULT NULL, modify_count INTEGER DEFAULT NULL, delete_count INTEGER DEFAULT NULL, changes_count INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_A1696B18B9CA839A FOREIGN KEY (mapper_id) REFERENCES mapper (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO changeset (id, mapper_id, editor, comment, extent, created_at, locale, reasons, harmful, suspect, checked, create_count, modify_count, delete_count, changes_count) SELECT id, mapper_id, editor, comment, extent, created_at, locale, reasons, harmful, suspect, checked, create_count, modify_count, delete_count, changes_count FROM __temp__changeset');
        $this->addSql('DROP TABLE __temp__changeset');
        $this->addSql('CREATE INDEX IDX_A1696B18B9CA839A ON changeset (mapper_id)');
        $this->addSql('DROP INDEX UNIQ_38B5A4E4D109C490');
        $this->addSql('CREATE TEMPORARY TABLE __temp__mapper AS SELECT id, changesets_count, account_created, region, display_name, status, image FROM mapper');
        $this->addSql('DROP TABLE mapper');
        $this->addSql('CREATE TABLE mapper (id INTEGER NOT NULL, changesets_count INTEGER NOT NULL, account_created DATETIME NOT NULL, region VARCHAR(255) NOT NULL COLLATE BINARY, display_name VARCHAR(255) NOT NULL COLLATE BINARY, status VARCHAR(255) NOT NULL COLLATE BINARY, image VARCHAR(255) DEFAULT NULL COLLATE BINARY, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO mapper (id, changesets_count, account_created, region, display_name, status, image) SELECT id, changesets_count, account_created, region, display_name, status, image FROM __temp__mapper');
        $this->addSql('DROP TABLE __temp__mapper');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP INDEX IDX_A1696B18B9CA839A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__changeset AS SELECT id, mapper_id, editor, comment, reasons, changes_count, extent, created_at, locale, create_count, modify_count, delete_count, harmful, suspect, checked FROM changeset');
        $this->addSql('DROP TABLE changeset');
        $this->addSql('CREATE TABLE changeset (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, mapper_id INTEGER NOT NULL, editor VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, reasons CLOB DEFAULT NULL --(DC2Type:array)
        , changes_count INTEGER NOT NULL, extent CLOB NOT NULL --(DC2Type:array)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , locale VARCHAR(255) DEFAULT NULL, create_count INTEGER DEFAULT NULL, modify_count INTEGER DEFAULT NULL, delete_count INTEGER DEFAULT NULL, harmful BOOLEAN DEFAULT NULL, suspect BOOLEAN DEFAULT NULL, checked BOOLEAN DEFAULT NULL)');
        $this->addSql('INSERT INTO changeset (id, mapper_id, editor, comment, reasons, changes_count, extent, created_at, locale, create_count, modify_count, delete_count, harmful, suspect, checked) SELECT id, mapper_id, editor, comment, reasons, changes_count, extent, created_at, locale, create_count, modify_count, delete_count, harmful, suspect, checked FROM __temp__changeset');
        $this->addSql('DROP TABLE __temp__changeset');
        $this->addSql('CREATE INDEX IDX_A1696B18B9CA839A ON changeset (mapper_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__mapper AS SELECT id, region, display_name, account_created, changesets_count, status, image FROM mapper');
        $this->addSql('DROP TABLE mapper');
        $this->addSql('CREATE TABLE mapper (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, region VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, account_created DATETIME NOT NULL, changesets_count INTEGER NOT NULL, status VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, welcome_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO mapper (id, region, display_name, account_created, changesets_count, status, image) SELECT id, region, display_name, account_created, changesets_count, status, image FROM __temp__mapper');
        $this->addSql('DROP TABLE __temp__mapper');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38B5A4E4D109C490 ON mapper (welcome_id)');
    }
}
