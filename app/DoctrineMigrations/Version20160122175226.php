<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160122175226 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE config_categories SET fromActual = '0' WHERE slug = 'ventilation'");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
