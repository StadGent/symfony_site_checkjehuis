<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160314153517 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE config_categories SET fromActual = 1 WHERE slug IN('heating', 'heating_elec')");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
