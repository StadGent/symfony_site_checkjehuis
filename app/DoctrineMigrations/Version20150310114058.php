<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150310114058 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE houses ADD defaultRoofIfFlat_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CBBE6B401D FOREIGN KEY (defaultRoofIfFlat_id) REFERENCES default_roofs (id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CBBE6B401D ON houses (defaultRoofIfFlat_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CBBE6B401D');
        $this->addSql('DROP INDEX IDX_95D7F5CBBE6B401D ON houses');
        $this->addSql('ALTER TABLE houses DROP defaultRoofIfFlat_id');
    }
}
