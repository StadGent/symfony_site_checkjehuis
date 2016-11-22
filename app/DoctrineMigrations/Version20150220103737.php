<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150220103737 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("DELETE FROM config_transformations WHERE inverse = 1 AND fromConfig_id IN (32, 33, 34, 35, 36)");
        $this->addSql("UPDATE config_categories SET hasInverseMatrix = 0 WHERE slug = 'heating'");
    }

    public function down(Schema $schema)
    {

    }
}
