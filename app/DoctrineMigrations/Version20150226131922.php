<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150226131922 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // fix for incorrect column names on join table
        $this->addSql('ALTER TABLE house_config DROP FOREIGN KEY FK_6737B6F524DB0683;');
        $this->addSql('ALTER TABLE house_config DROP FOREIGN KEY FK_6737B6F56BB74515;');
        $this->addSql('ALTER TABLE house_config DROP PRIMARY KEY;');
        $this->addSql('ALTER TABLE house_upgrade_config DROP FOREIGN KEY FK_2F646F176BB74515;');
        $this->addSql('ALTER TABLE house_upgrade_config DROP FOREIGN KEY FK_2F646F177D2FC74;');
        $this->addSql('ALTER TABLE house_upgrade_config DROP PRIMARY KEY;');
        $this->addSql('ALTER TABLE house_config ADD COLUMN temp int(10) unsigned NOT NULL DEFAULT 0;');
        $this->addSql('ALTER TABLE house_upgrade_config ADD COLUMN temp int(10) unsigned NOT NULL DEFAULT 0;');
        $this->addSql('UPDATE house_config SET temp = house_id;');
        $this->addSql('UPDATE house_config SET house_id = config_id;');
        $this->addSql('UPDATE house_config SET config_id = temp;');
        $this->addSql('UPDATE house_upgrade_config SET temp = house_id;');
        $this->addSql('UPDATE house_upgrade_config SET house_id = upgrade_config_id;');
        $this->addSql('UPDATE house_upgrade_config SET upgrade_config_id = temp;');
        $this->addSql('ALTER TABLE house_upgrade_config DROP temp;');
        $this->addSql('ALTER TABLE house_config DROP temp;');
        $this->addSql('ALTER TABLE house_config ADD CONSTRAINT FK_6737B6F524DB0683 FOREIGN KEY (config_id) REFERENCES config_choices (id);');
        $this->addSql('ALTER TABLE house_config ADD CONSTRAINT FK_6737B6F56BB74515 FOREIGN KEY (house_id) REFERENCES houses (id);');
        $this->addSql('ALTER TABLE house_config ADD PRIMARY KEY (house_id, config_id);');
        $this->addSql('ALTER TABLE house_upgrade_config ADD CONSTRAINT FK_2F646F176BB74515 FOREIGN KEY (house_id) REFERENCES houses (id);');
        $this->addSql('ALTER TABLE house_upgrade_config ADD CONSTRAINT FK_2F646F177D2FC74 FOREIGN KEY (upgrade_config_id) REFERENCES config_choices (id);');
        $this->addSql('ALTER TABLE house_upgrade_config ADD PRIMARY KEY (house_id, upgrade_config_id);');

        // removing previous attic floor stuff, adding as regular option in roof category
        $this->addSql('ALTER TABLE config_categories DROP custom');

        $this->addSql('DELETE FROM house_config WHERE config_id = 43');
        $this->addSql('DELETE FROM house_upgrade_config WHERE upgrade_config_id = 43');
        $this->addSql('DELETE FROM config_transformations WHERE id = 95');
        $this->addSql('DELETE FROM config_subsidies WHERE config_id = 43');
        $this->addSql('DELETE FROM config_transformations WHERE fromConfig_id = 43 OR toConfig_id = 43');
        $this->addSql('DELETE FROM config_choices WHERE id = 43');
        $this->addSql('UPDATE config_choices SET ordering = 8 WHERE id = 7');
        $this->addSql('UPDATE config_choices SET category_id = 1, ordering = 7, label = \'Goed geïsoleerd: 30cm op de zoldervloer\', `default` = 0, possibleCurrent = 1, possibleUpgrade = 1, relatedCost_id = 20 WHERE id = 42');
        $this->addSql('UPDATE config_choices SET label = \'Goed geïsoleerd: 18 cm of R=4.7m²k/W\' WHERE id = 5');
        $this->addSql('DELETE FROM config_categories WHERE id = 8');
        $this->addSql('INSERT INTO config_transformations (id, value, unit, inverse, fromConfig_id, toConfig_id) VALUES
          (NULL, 20, \'%\', 0, 1, 42),
          (NULL, 36, \'%\', 0, 2, 42),
          (NULL, 44, \'%\', 0, 3, 42),
          (NULL, 54, \'%\', 0, 4, 42),
          (NULL, 64, \'%\', 0, 5, 42),
          (NULL, 64, \'%\', 0, 6, 42),
          (NULL, 64, \'%\', 0, 42, 7)
        ;');
        $this->addSql('INSERT INTO config_subsidies (config_id, subsidy_id) VALUES
          (42, 91),
          (42, 92),
          (42, 93),
          (42, 94),
          (42, 95)
        ;');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE config_categories ADD custom TINYINT(1) NOT NULL');
    }
}
