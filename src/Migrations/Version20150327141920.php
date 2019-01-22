<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150327141920 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE config_choices SET co2Factor = '0'");
    }

    public function down(Schema $schema)
    {
    }
}
