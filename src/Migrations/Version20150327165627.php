<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150327165627 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE subsidies SET slug = 'window_0_8' WHERE label = 'schrijnwerk (0.8)'");
        $this->addSql("UPDATE subsidies SET slug = 'window_1_1' WHERE label = 'schrijnwerk (1.1)'");
    }

    public function down(Schema $schema)
    {
    }
}
