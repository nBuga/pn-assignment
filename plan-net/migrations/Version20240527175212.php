<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240527175212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_prize RENAME INDEX idx_b30a70999d86650f TO IDX_B30A7099A76ED395');
        $this->addSql('ALTER TABLE user_prize RENAME INDEX idx_b30a70993779bc37 TO IDX_B30A7099BBE43214');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_prize RENAME INDEX idx_b30a7099bbe43214 TO IDX_B30A70993779BC37');
        $this->addSql('ALTER TABLE user_prize RENAME INDEX idx_b30a7099a76ed395 TO IDX_B30A70999D86650F');
    }
}
