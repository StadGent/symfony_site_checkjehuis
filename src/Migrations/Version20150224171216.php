<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150224171216 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE build_costs SET ordering = ordering + 1 WHERE ordering > 4");

        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/005/data_build_costs.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/005/data_subsidies.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/005/data_config_subsidies.sql'));

        $this->addSql("UPDATE config_choices SET relatedCost_id = 20 WHERE id = 43");
    }

    public function down(Schema $schema)
    {

    }
}
