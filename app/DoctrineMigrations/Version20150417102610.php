<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150417102610 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE config_choices SET label='Goed geïsoleerd: 18cm of R=4.7m²k/W op de zoldervloer' WHERE category_id = 1 AND ordering = 6");
        $this->addSql("UPDATE config_choices SET ordering=0 WHERE category_id = 1 AND ordering = 6");
        $this->addSql("UPDATE config_choices SET ordering=6 WHERE category_id = 1 AND ordering = 5");
        $this->addSql("UPDATE config_choices SET ordering=5 WHERE category_id = 1 AND ordering = 0");

        // we have a hole in the ordering numbering...
        $this->addSql("UPDATE config_choices SET ordering=8 WHERE category_id = 1 AND ordering = 9");
    }

    public function down(Schema $schema)
    {

    }
}
