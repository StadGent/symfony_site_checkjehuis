<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150223124307 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE houses CHANGE surfaceroofinclined surfaceRoofExtra DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE houses ADD extraConfigRoof_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE houses ADD extraUpgradeRoof_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CB2D2255D3 FOREIGN KEY (extraConfigRoof_id) REFERENCES config_choices (id)');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CB7036B2E8 FOREIGN KEY (extraUpgradeRoof_id) REFERENCES config_choices (id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CB2D2255D3 ON houses (extraConfigRoof_id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CB7036B2E8 ON houses (extraUpgradeRoof_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE houses CHANGE surfaceroofextra surfaceRoofInclined DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CB2D2255D3');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CB7036B2E8');
        $this->addSql('DROP INDEX IDX_95D7F5CB2D2255D3 ON houses');
        $this->addSql('DROP INDEX IDX_95D7F5CB7036B2E8 ON houses');
        $this->addSql('ALTER TABLE houses DROP extraConfigRoof_id');
        $this->addSql('ALTER TABLE houses DROP extraUpgradeRoof_id');
    }
}
