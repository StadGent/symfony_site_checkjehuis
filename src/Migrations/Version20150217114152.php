<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150217114152 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE default_energy ADD electricityHeatPump DOUBLE PRECISION NOT NULL');
        $this->addSql("UPDATE default_energy SET electricityHeatPump = 6500 WHERE size = 'small'");
        $this->addSql("UPDATE default_energy SET electricityHeatPump = 7000 WHERE size = 'medium'");
        $this->addSql("UPDATE default_energy SET electricityHeatPump = 9000 WHERE size = 'large'");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE default_energy DROP electricityHeatPump');
    }
}
