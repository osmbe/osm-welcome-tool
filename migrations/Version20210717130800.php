<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210717130800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE changeset (id INTEGER NOT NULL, uid VARCHAR(255) NOT NULL, editor VARCHAR(255) DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, tags CLOB NOT NULL --(DC2Type:array)
        , create_count INTEGER NOT NULL, modify_count INTEGER NOT NULL, delete_count INTEGER NOT NULL, checked BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TEMPORARY TABLE __temp__mapper AS SELECT id, username, changesets_count, first_changeset_id, account_created, region FROM mapper');
        $this->addSql('DROP TABLE mapper');
        $this->addSql('CREATE TABLE mapper (id INTEGER NOT NULL, changesets_count INTEGER NOT NULL, account_created DATETIME NOT NULL, region VARCHAR(255) NOT NULL COLLATE BINARY, display_name VARCHAR(255) NOT NULL, first_changeset INTEGER NOT NULL, locale VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO mapper (id, display_name, changesets_count, first_changeset, account_created, region) SELECT id, username, changesets_count, first_changeset_id, account_created, region FROM __temp__mapper');
        $this->addSql('DROP TABLE __temp__mapper');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE changeset');
        $this->addSql('CREATE TEMPORARY TABLE __temp__mapper AS SELECT id, region, account_created, changesets_count, first_changeset FROM mapper');
        $this->addSql('DROP TABLE mapper');
        $this->addSql('CREATE TABLE mapper (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, region VARCHAR(255) NOT NULL, account_created DATETIME NOT NULL, changesets_count INTEGER NOT NULL, first_changeset_id INTEGER NOT NULL, username VARCHAR(255) NOT NULL COLLATE BINARY, first_changeset_created DATETIME NOT NULL)');
        $this->addSql('INSERT INTO mapper (id, region, account_created, changesets_count, first_changeset_id) SELECT id, region, account_created, changesets_count, first_changeset FROM __temp__mapper');
        $this->addSql('DROP TABLE __temp__mapper');
    }
}
