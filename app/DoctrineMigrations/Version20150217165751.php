<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150217165751 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE config_choices SET  label = 'Slecht geïsoleerd: 6 cm of R=1,6m²k/W' WHERE id = 2;");
        $this->addSql("UPDATE config_choices SET  label = 'Slecht geïsoleerd: 10 cm of R=2,6m²k/W' WHERE id = 3;");
        $this->addSql("UPDATE config_choices SET  label = 'Matig geïsoleerd: 12 cm of R=3.2m²k/W' WHERE id = 4;");
        $this->addSql("UPDATE config_choices SET  label = 'Goed geïsoleerd: 18 cm of R=4.2m²k/W' WHERE id = 5;");
        $this->addSql("UPDATE config_choices SET  label = 'Goed geïsoleerd: 24 cm of R=6.3m²k/W' WHERE id = 6;");
        $this->addSql("UPDATE config_choices SET  label = 'Perfect geïsoleerd: 30 cm of R=7.9m²k/W' WHERE id = 7;");

        $this->addSql("UPDATE config_choices SET label = 'overal enkel glas (5,5W/m²K)' WHERE id =21;");
        $this->addSql("UPDATE config_choices SET label = 'overal gewoon dubbel glas (2,5W/m²K)' WHERE id =22;");
        $this->addSql("UPDATE config_choices SET label = 'overal hoogrendementsglas (1,1W/m²K)' WHERE id =23;");
        $this->addSql("UPDATE config_choices SET label = 'overal super isolerend glas (0,8W/m²K)' WHERE id =24;");
    }

    public function down(Schema $schema)
    {
    }
}
