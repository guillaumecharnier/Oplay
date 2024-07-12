<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240712151544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C26DB921E');
        $this->addSql('DROP INDEX IDX_232B318C26DB921E ON game');
        $this->addSql('ALTER TABLE game DROP game_order_id');
        $this->addSql('ALTER TABLE game_order ADD game_id INT NOT NULL, ADD order_id INT NOT NULL, CHANGE total_price total_price NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE game_order ADD CONSTRAINT FK_C71AEA17E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_order ADD CONSTRAINT FK_C71AEA178D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_C71AEA17E48FD905 ON game_order (game_id)');
        $this->addSql('CREATE INDEX IDX_C71AEA178D9F6D38 ON game_order (order_id)');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939826DB921E');
        $this->addSql('DROP INDEX IDX_F529939826DB921E ON `order`');
        $this->addSql('ALTER TABLE `order` DROP game_order_id, CHANGE created_at created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game ADD game_order_id INT NOT NULL');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C26DB921E FOREIGN KEY (game_order_id) REFERENCES game_order (id)');
        $this->addSql('CREATE INDEX IDX_232B318C26DB921E ON game (game_order_id)');
        $this->addSql('ALTER TABLE `order` ADD game_order_id INT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939826DB921E FOREIGN KEY (game_order_id) REFERENCES game_order (id)');
        $this->addSql('CREATE INDEX IDX_F529939826DB921E ON `order` (game_order_id)');
        $this->addSql('ALTER TABLE game_order DROP FOREIGN KEY FK_C71AEA17E48FD905');
        $this->addSql('ALTER TABLE game_order DROP FOREIGN KEY FK_C71AEA178D9F6D38');
        $this->addSql('DROP INDEX IDX_C71AEA17E48FD905 ON game_order');
        $this->addSql('DROP INDEX IDX_C71AEA178D9F6D38 ON game_order');
        $this->addSql('ALTER TABLE game_order DROP game_id, DROP order_id, CHANGE total_price total_price DOUBLE PRECISION NOT NULL');
    }
}
