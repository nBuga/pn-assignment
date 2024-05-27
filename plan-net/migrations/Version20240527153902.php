<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240527153902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_prize (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, prize_id INT NOT NULL, received_prize_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B30A70999D86650F (user_id), INDEX IDX_B30A70993779BC37 (prize_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_prize ADD CONSTRAINT FK_B30A70999D86650F FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_prize ADD CONSTRAINT FK_B30A70993779BC37 FOREIGN KEY (prize_id) REFERENCES prize (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_prize DROP FOREIGN KEY FK_B30A70999D86650F');
        $this->addSql('ALTER TABLE user_prize DROP FOREIGN KEY FK_B30A70993779BC37');
        $this->addSql('DROP TABLE user_prize');
    }
}
