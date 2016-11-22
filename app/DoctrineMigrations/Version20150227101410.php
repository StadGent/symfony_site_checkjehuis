<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150227101410 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO parameters (id, slug, label, value, unit) VALUES (NULL, 'subsidy_ceiling_roof_gent', 'Plafond daksubsidies Stad Gent', '1000', '€')");
        $this->addSql('UPDATE config_choices SET ordering = 6, label = \'Goed geïsoleerd: 30cm op de zoldervloer\' WHERE id = 42');
    }

    public function down(Schema $schema)
    {
    }
}
