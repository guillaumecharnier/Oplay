<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240712145019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_order ADD orders_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_order ADD CONSTRAINT FK_C71AEA17CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_C71AEA17CFFE9AD6 ON game_order (orders_id)');
        $this->addSql('ALTER TABLE `order` ADD games_id INT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939897FFC673 FOREIGN KEY (games_id) REFERENCES game (id)');
        $this->addSql('CREATE INDEX IDX_F529939897FFC673 ON `order` (games_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939897FFC673');
        $this->addSql('DROP INDEX IDX_F529939897FFC673 ON `order`');
        $this->addSql('ALTER TABLE `order` DROP games_id');
        $this->addSql('ALTER TABLE game_order DROP FOREIGN KEY FK_C71AEA17CFFE9AD6');
        $this->addSql('DROP INDEX IDX_C71AEA17CFFE9AD6 ON game_order');
        $this->addSql('ALTER TABLE game_order DROP orders_id');
    }
}
