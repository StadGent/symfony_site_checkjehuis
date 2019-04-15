<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150327170248 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("UPDATE renewables SET co2Factor = '0'");
    }

    public function down(Schema $schema) : void
    {
    }
}
