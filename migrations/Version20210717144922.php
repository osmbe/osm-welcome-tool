<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210717144922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__changeset AS SELECT id, editor, comment, tags, create_count, modify_count, delete_count, checked FROM changeset');
        $this->addSql('DROP TABLE changeset');
        $this->addSql('CREATE TABLE changeset (id INTEGER NOT NULL, mapper_id INTEGER NOT NULL, editor VARCHAR(255) DEFAULT NULL COLLATE BINARY, comment VARCHAR(255) DEFAULT NULL COLLATE BINARY, tags CLOB NOT NULL COLLATE BINARY --(DC2Type:array)
        , create_count INTEGER NOT NULL, modify_count INTEGER NOT NULL, delete_count INTEGER NOT NULL, checked BOOLEAN NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_A1696B18B9CA839A FOREIGN KEY (mapper_id) REFERENCES mapper (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO changeset (id, editor, comment, tags, create_count, modify_count, delete_count, checked) SELECT id, editor, comment, tags, create_count, modify_count, delete_count, checked FROM __temp__changeset');
        $this->addSql('DROP TABLE __temp__changeset');
        $this->addSql('CREATE INDEX IDX_A1696B18B9CA839A ON changeset (mapper_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__mapper AS SELECT id, changesets_count, account_created, region, display_name, first_changeset, locale, status FROM mapper');
        $this->addSql('DROP TABLE mapper');
        $this->addSql('CREATE TABLE mapper (id INTEGER NOT NULL, first_changeset_id INTEGER NOT NULL, welcome_id INTEGER DEFAULT NULL, changesets_count INTEGER NOT NULL, account_created DATETIME NOT NULL, region VARCHAR(255) NOT NULL COLLATE BINARY, display_name VARCHAR(255) NOT NULL COLLATE BINARY, locale VARCHAR(255) DEFAULT NULL COLLATE BINARY, status VARCHAR(255) NOT NULL COLLATE BINARY, PRIMARY KEY(id), CONSTRAINT FK_38B5A4E4B3E70A58 FOREIGN KEY (first_changeset_id) REFERENCES changeset (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_38B5A4E4D109C490 FOREIGN KEY (welcome_id) REFERENCES welcome (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO mapper (id, changesets_count, account_created, region, display_name, first_changeset_id, locale, status) SELECT id, changesets_count, account_created, region, display_name, first_changeset, locale, status FROM __temp__mapper');
        $this->addSql('DROP TABLE __temp__mapper');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38B5A4E4B3E70A58 ON mapper (first_changeset_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_38B5A4E4D109C490 ON mapper (welcome_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__welcome AS SELECT id, datetime FROM welcome');
        $this->addSql('DROP TABLE welcome');
        $this->addSql('CREATE TABLE welcome (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, datetime DATETIME NOT NULL)');
        $this->addSql('INSERT INTO welcome (id, datetime) SELECT id, datetime FROM __temp__welcome');
        $this->addSql('DROP TABLE __temp__welcome');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_A1696B18B9CA839A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__changeset AS SELECT id, editor, comment, tags, create_count, modify_count, delete_count, checked FROM changeset');
        $this->addSql('DROP TABLE changeset');
        $this->addSql('CREATE TABLE changeset (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, editor VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, tags CLOB NOT NULL --(DC2Type:array)
        , create_count INTEGER NOT NULL, modify_count INTEGER NOT NULL, delete_count INTEGER NOT NULL, checked BOOLEAN NOT NULL, uid VARCHAR(255) NOT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO changeset (id, editor, comment, tags, create_count, modify_count, delete_count, checked) SELECT id, editor, comment, tags, create_count, modify_count, delete_count, checked FROM __temp__changeset');
        $this->addSql('DROP TABLE __temp__changeset');
        $this->addSql('DROP INDEX UNIQ_38B5A4E4B3E70A58');
        $this->addSql('DROP INDEX UNIQ_38B5A4E4D109C490');
        $this->addSql('CREATE TEMPORARY TABLE __temp__mapper AS SELECT id, first_changeset_id, region, display_name, account_created, changesets_count, locale, status FROM mapper');
        $this->addSql('DROP TABLE mapper');
        $this->addSql('CREATE TABLE mapper (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, region VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, account_created DATETIME NOT NULL, changesets_count INTEGER NOT NULL, locale VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, first_changeset INTEGER NOT NULL)');
        $this->addSql('INSERT INTO mapper (id, first_changeset, region, display_name, account_created, changesets_count, locale, status) SELECT id, first_changeset_id, region, display_name, account_created, changesets_count, locale, status FROM __temp__mapper');
        $this->addSql('DROP TABLE __temp__mapper');
        $this->addSql('ALTER TABLE welcome ADD COLUMN uid INTEGER NOT NULL');
        $this->addSql('ALTER TABLE welcome ADD COLUMN by_uid INTEGER NOT NULL');
        $this->addSql('ALTER TABLE welcome ADD COLUMN by_display_name VARCHAR(255) NOT NULL COLLATE BINARY');
    }
}
