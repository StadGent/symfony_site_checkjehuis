<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150703133208 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("UPDATE renewables SET value=114, unit='kWh/m²' WHERE slug='solar_panels'");
        $this->addSql("UPDATE build_costs SET value=254, unit='m²' WHERE slug='solar_panels'");
        $this->addSql("UPDATE parameters SET value=30, unit='m²', slug='solar_panel_surface', label='Zonnepanelen' WHERE slug='kwh_peek'");
    }

    public function down(Schema $schema) : void
    {
    }
}
