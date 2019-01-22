<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160118104240 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE default_energy SET electricityHeatPump = 0');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
