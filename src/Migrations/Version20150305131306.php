<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150305131306 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('UPDATE config_choices SET ordering = ordering + 1 WHERE ordering > 5 AND category_id = 1');
        $this->addSql('UPDATE config_choices SET ordering = 6, label = \'Goed geïsoleerd: 18cm op de zoldervloer\' WHERE id = 42');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
