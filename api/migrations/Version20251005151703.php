<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251005151703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE login_event (id SERIAL NOT NULL, user_id INT NOT NULL, occurred_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3DDECD33A76ED395 ON login_event (user_id)');
        $this->addSql('CREATE INDEX login_event_occurred_idx ON login_event (occurred_at)');
        $this->addSql('COMMENT ON COLUMN login_event.occurred_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, password_reset_token_hash VARCHAR(64) DEFAULT NULL, password_reset_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".password_reset_expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE login_event ADD CONSTRAINT FK_3DDECD33A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE login_event DROP CONSTRAINT FK_3DDECD33A76ED395');
        $this->addSql('DROP TABLE login_event');
        $this->addSql('DROP TABLE "user"');
    }
}
