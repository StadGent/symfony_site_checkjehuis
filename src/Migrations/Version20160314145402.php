<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160314145402 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql("UPDATE config_categories SET fromActual = 0 WHERE slug IN('heating', 'heating_elec')");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
    }
}
