<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240901000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initialize database';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE mapper (id BIGINT NOT NULL, display_name TEXT DEFAULT NULL, account_created TIMESTAMP WITH TIME ZONE DEFAULT NULL, changesets_count BIGINT DEFAULT NULL, status TEXT DEFAULT NULL, image TEXT DEFAULT NULL, PRIMARY KEY(id))');

        $this->addSql('CREATE TABLE users (id BIGINT NOT NULL, display_name TEXT DEFAULT NULL, roles TEXT DEFAULT NULL, image TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX idx_users_display_name ON users (display_name)');

        $this->addSql('CREATE TABLE note (id BIGSERIAL, mapper_id BIGINT DEFAULT NULL REFERENCES mapper(id), author_id BIGINT DEFAULT NULL REFERENCES users(id), date TIMESTAMP WITH TIME ZONE DEFAULT NULL, text TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_note_mapper_id ON note (mapper_id)');
        $this->addSql('CREATE INDEX idx_note_author_id ON note (author_id)');

        $this->addSql('CREATE TABLE region (id TEXT NOT NULL, last_update TIMESTAMP WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');

        $this->addSql('CREATE TABLE mapper_region (mapper_id BIGINT NOT NULL REFERENCES mapper(id), region_id TEXT NOT NULL REFERENCES region(id), PRIMARY KEY(mapper_id, region_id))');
        $this->addSql('CREATE INDEX idx_mapper_region_mapper_id ON mapper_region (mapper_id)');
        $this->addSql('CREATE INDEX idx_mapper_region_region_id ON mapper_region (region_id)');

        $this->addSql('CREATE TABLE changeset (id BIGINT NOT NULL, mapper_id BIGINT DEFAULT NULL REFERENCES mapper(id), editor TEXT DEFAULT NULL, comment TEXT DEFAULT NULL, reasons TEXT DEFAULT NULL, changes_count BIGINT DEFAULT NULL, extent TEXT DEFAULT NULL, created_at TIMESTAMP WITH TIME ZONE DEFAULT NULL, locale TEXT DEFAULT NULL, create_count BIGINT DEFAULT NULL, modify_count BIGINT DEFAULT NULL, delete_count BIGINT DEFAULT NULL, harmful BOOLEAN DEFAULT NULL, suspect BOOLEAN DEFAULT NULL, checked BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_changeset_mapper_id ON changeset (mapper_id)');

        $this->addSql('CREATE TABLE welcome (id BIGSERIAL, mapper_id BIGINT DEFAULT NULL REFERENCES mapper(id), user_id BIGINT DEFAULT NULL REFERENCES users(id), date TIMESTAMP WITH TIME ZONE DEFAULT NULL, reply TIMESTAMP WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_welcome_mapper_id ON welcome (mapper_id)');
        $this->addSql('CREATE INDEX idx_welcome_user_id ON welcome (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE mapper_region');
        $this->addSql('DROP TABLE changeset');
        $this->addSql('DROP TABLE welcome');
        $this->addSql('DROP TABLE mapper');
        $this->addSql('DROP TABLE users');
    }
}
