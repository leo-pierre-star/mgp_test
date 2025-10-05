<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251005140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add password reset token hash & expiration fields';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ADD password_reset_token_hash VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD password_reset_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" DROP password_reset_token_hash');
        $this->addSql('ALTER TABLE "user" DROP password_reset_expires_at');
    }
}
