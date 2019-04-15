<?php
namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160215115049 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql("UPDATE config_choices set relatedCost_id = 22 WHERE id=45");
        $this->addSql("UPDATE config_choices set relatedCost_id = 23 WHERE id=46");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
    }
}
