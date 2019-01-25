<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150310181655 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("INSERT INTO content (id, slug, label, value, canDeactivate, active) VALUES (NULL, 'heat_pump_not_allowed', 'Warmtepomp niet toegestaan', '<p>Uw huis voldoet niet aan de voorwaarden om een warmtepomp te selecteren. De selectie is ongedaan gemaakt.</p><p>Het dak en de ramen moeten groen zijn.</p>', '0', '1')");
    }

    public function down(Schema $schema) : void
    {
    }
}
