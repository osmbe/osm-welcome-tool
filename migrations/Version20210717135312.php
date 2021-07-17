<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210717135312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE welcome (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, uid INTEGER NOT NULL, by_uid INTEGER NOT NULL, by_display_name VARCHAR(255) NOT NULL, datetime DATETIME NOT NULL)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__changeset AS SELECT id, uid, editor, comment, tags, create_count, modify_count, delete_count, checked FROM changeset');
        $this->addSql('DROP TABLE changeset');
        $this->addSql('CREATE TABLE changeset (id INTEGER NOT NULL, uid VARCHAR(255) NOT NULL COLLATE BINARY, editor VARCHAR(255) DEFAULT NULL COLLATE BINARY, comment VARCHAR(255) DEFAULT NULL COLLATE BINARY, tags CLOB NOT NULL COLLATE BINARY --(DC2Type:array)
        , create_count INTEGER NOT NULL, modify_count INTEGER NOT NULL, delete_count INTEGER NOT NULL, checked BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO changeset (id, uid, editor, comment, tags, create_count, modify_count, delete_count, checked) SELECT id, uid, editor, comment, tags, create_count, modify_count, delete_count, checked FROM __temp__changeset');
        $this->addSql('DROP TABLE __temp__changeset');
        $this->addSql('CREATE TEMPORARY TABLE __temp__mapper AS SELECT id, changesets_count, account_created, region, display_name, first_changeset, locale, status FROM mapper');
        $this->addSql('DROP TABLE mapper');
        $this->addSql('CREATE TABLE mapper (id INTEGER NOT NULL, changesets_count INTEGER NOT NULL, account_created DATETIME NOT NULL, region VARCHAR(255) NOT NULL COLLATE BINARY, display_name VARCHAR(255) NOT NULL COLLATE BINARY, first_changeset INTEGER NOT NULL, locale VARCHAR(255) DEFAULT NULL COLLATE BINARY, status VARCHAR(255) NOT NULL COLLATE BINARY, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO mapper (id, changesets_count, account_created, region, display_name, first_changeset, locale, status) SELECT id, changesets_count, account_created, region, display_name, first_changeset, locale, status FROM __temp__mapper');
        $this->addSql('DROP TABLE __temp__mapper');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE welcome');
        $this->addSql('CREATE TEMPORARY TABLE __temp__changeset AS SELECT id, uid, editor, comment, tags, create_count, modify_count, delete_count, checked FROM changeset');
        $this->addSql('DROP TABLE changeset');
        $this->addSql('CREATE TABLE changeset (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, uid VARCHAR(255) NOT NULL, editor VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, tags CLOB NOT NULL --(DC2Type:array)
        , create_count INTEGER NOT NULL, modify_count INTEGER NOT NULL, delete_count INTEGER NOT NULL, checked BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO changeset (id, uid, editor, comment, tags, create_count, modify_count, delete_count, checked) SELECT id, uid, editor, comment, tags, create_count, modify_count, delete_count, checked FROM __temp__changeset');
        $this->addSql('DROP TABLE __temp__changeset');
        $this->addSql('CREATE TEMPORARY TABLE __temp__mapper AS SELECT id, region, display_name, account_created, changesets_count, first_changeset, locale, status FROM mapper');
        $this->addSql('DROP TABLE mapper');
        $this->addSql('CREATE TABLE mapper (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, region VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, account_created DATETIME NOT NULL, changesets_count INTEGER NOT NULL, first_changeset INTEGER NOT NULL, locale VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO mapper (id, region, display_name, account_created, changesets_count, first_changeset, locale, status) SELECT id, region, display_name, account_created, changesets_count, first_changeset, locale, status FROM __temp__mapper');
        $this->addSql('DROP TABLE __temp__mapper');
    }
}
