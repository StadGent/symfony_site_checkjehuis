<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150224102213 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE config_categories ADD custom TINYINT(1) NOT NULL');
        $this->addSql('UPDATE config_categories SET custom = 0');

        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/004/data_config_categories.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/004/data_config_choices.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/004/data_config_transformations.sql'));
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE config_categories DROP custom');
    }
}
