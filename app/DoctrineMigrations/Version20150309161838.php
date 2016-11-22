<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150309161838 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE subsidies SET label = \'zoldervloerisolatie\' WHERE slug = \'attic_floor\'');
    }

    public function down(Schema $schema)
    {
    }
}
