<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20141203145948 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE default_roofs (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, size VARCHAR(255) NOT NULL, inclined VARCHAR(255) NOT NULL, surface DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE default_surfaces (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, size VARCHAR(255) NOT NULL, livingArea DOUBLE PRECISION NOT NULL, floor DOUBLE PRECISION NOT NULL, facade DOUBLE PRECISION NOT NULL, window DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE houses (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, lastKnownRoute VARCHAR(255) DEFAULT NULL, lastUpdate DATETIME NOT NULL, address VARCHAR(255) DEFAULT NULL, newsletter TINYINT(1) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, buildingType VARCHAR(255) NOT NULL, roofType VARCHAR(255) NOT NULL, size VARCHAR(255) NOT NULL, ownership VARCHAR(255) NOT NULL, year VARCHAR(255) NOT NULL, occupants VARCHAR(255) NOT NULL, consumptionGas DOUBLE PRECISION DEFAULT NULL, consumptionElec DOUBLE PRECISION DEFAULT NULL, surfaceFloor DOUBLE PRECISION DEFAULT NULL, surfaceFacade DOUBLE PRECISION DEFAULT NULL, surfaceWindow DOUBLE PRECISION DEFAULT NULL, surfaceRoof DOUBLE PRECISION DEFAULT NULL, surfaceRoofInclined DOUBLE PRECISION DEFAULT NULL, electricHeating TINYINT(1) NOT NULL, hasWindroof TINYINT(1) NOT NULL, placeWindroof TINYINT(1) NOT NULL, solarPanelsKWHPiek DOUBLE PRECISION NOT NULL, defaultEnergy_id INT DEFAULT NULL, defaultSurface_id INT DEFAULT NULL, defaultRoof_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_95D7F5CB5F37A13B (token), INDEX IDX_95D7F5CBA4A802A0 (defaultEnergy_id), INDEX IDX_95D7F5CB59B27F5E (defaultSurface_id), INDEX IDX_95D7F5CB52C922DE (defaultRoof_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE house_config (config_id INT NOT NULL, house_id INT NOT NULL, INDEX IDX_6737B6F524DB0683 (config_id), INDEX IDX_6737B6F56BB74515 (house_id), PRIMARY KEY(config_id, house_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE house_renewable (renewable_id INT NOT NULL, house_id INT NOT NULL, INDEX IDX_AFFB24264CAFE380 (renewable_id), INDEX IDX_AFFB24266BB74515 (house_id), PRIMARY KEY(renewable_id, house_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE house_upgrade_config (upgrade_config_id INT NOT NULL, house_id INT NOT NULL, INDEX IDX_2F646F177D2FC74 (upgrade_config_id), INDEX IDX_2F646F176BB74515 (house_id), PRIMARY KEY(upgrade_config_id, house_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE house_upgrade_renewable (upgrade_renewable_id INT NOT NULL, house_id INT NOT NULL, INDEX IDX_A7055EB86BAB3765 (upgrade_renewable_id), INDEX IDX_A7055EB86BB74515 (house_id), PRIMARY KEY(upgrade_renewable_id, house_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE renewables (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, `label` VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, unit VARCHAR(255) NOT NULL, co2Factor DOUBLE PRECISION NOT NULL, relatedCost_id INT DEFAULT NULL, INDEX IDX_A83CF9DF87FD23C6 (relatedCost_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE build_costs (id INT AUTO_INCREMENT NOT NULL, ordering INT NOT NULL, slug VARCHAR(255) NOT NULL, `label` VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, unit VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE config_choices (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, ordering INT NOT NULL, `label` VARCHAR(255) NOT NULL, `default` TINYINT(1) NOT NULL, defaultUpToYear VARCHAR(255) NOT NULL, possibleCurrent TINYINT(1) NOT NULL, possibleUpgrade TINYINT(1) NOT NULL, costFactor DOUBLE PRECISION NOT NULL, co2Factor DOUBLE PRECISION NOT NULL, relatedCost_id INT DEFAULT NULL, INDEX IDX_70D254C912469DE2 (category_id), INDEX IDX_70D254C987FD23C6 (relatedCost_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE config_subsidies (config_id INT NOT NULL, subsidy_id INT NOT NULL, INDEX IDX_8D385B7724DB0683 (config_id), INDEX IDX_8D385B77C7F0C12C (subsidy_id), PRIMARY KEY(config_id, subsidy_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE config_categories (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, `label` VARCHAR(255) NOT NULL, ordering INT NOT NULL, hasInverseMatrix TINYINT(1) NOT NULL, fromActual TINYINT(1) NOT NULL, percent DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subsidy_categories (id INT AUTO_INCREMENT NOT NULL, `label` VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE default_energy (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, size VARCHAR(255) NOT NULL, maxYear VARCHAR(255) NOT NULL, electricity DOUBLE PRECISION NOT NULL, gas DOUBLE PRECISION NOT NULL, electricHeating DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, `label` VARCHAR(255) NOT NULL, value LONGTEXT NOT NULL, canDeactivate TINYINT(1) NOT NULL, active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_FEC530A9989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subsidies (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, `label` VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, multiplier VARCHAR(255) NOT NULL, max INT NOT NULL, subsidyCategory_id INT DEFAULT NULL, INDEX IDX_ECEE6C4AD75064B4 (subsidyCategory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE config_transformations (id INT AUTO_INCREMENT NOT NULL, value DOUBLE PRECISION NOT NULL, unit VARCHAR(255) NOT NULL, inverse TINYINT(1) NOT NULL, fromConfig_id INT DEFAULT NULL, toConfig_id INT DEFAULT NULL, INDEX IDX_67E13D09CE0521DA (fromConfig_id), INDEX IDX_67E13D0945B35FEE (toConfig_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parameters (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, `label` VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, unit VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_69348FE989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CBA4A802A0 FOREIGN KEY (defaultEnergy_id) REFERENCES default_energy (id)');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CB59B27F5E FOREIGN KEY (defaultSurface_id) REFERENCES default_surfaces (id)');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CB52C922DE FOREIGN KEY (defaultRoof_id) REFERENCES default_roofs (id)');
        $this->addSql('ALTER TABLE house_config ADD CONSTRAINT FK_6737B6F524DB0683 FOREIGN KEY (config_id) REFERENCES houses (id)');
        $this->addSql('ALTER TABLE house_config ADD CONSTRAINT FK_6737B6F56BB74515 FOREIGN KEY (house_id) REFERENCES config_choices (id)');
        $this->addSql('ALTER TABLE house_renewable ADD CONSTRAINT FK_AFFB24264CAFE380 FOREIGN KEY (renewable_id) REFERENCES houses (id)');
        $this->addSql('ALTER TABLE house_renewable ADD CONSTRAINT FK_AFFB24266BB74515 FOREIGN KEY (house_id) REFERENCES renewables (id)');
        $this->addSql('ALTER TABLE house_upgrade_config ADD CONSTRAINT FK_2F646F177D2FC74 FOREIGN KEY (upgrade_config_id) REFERENCES houses (id)');
        $this->addSql('ALTER TABLE house_upgrade_config ADD CONSTRAINT FK_2F646F176BB74515 FOREIGN KEY (house_id) REFERENCES config_choices (id)');
        $this->addSql('ALTER TABLE house_upgrade_renewable ADD CONSTRAINT FK_A7055EB86BAB3765 FOREIGN KEY (upgrade_renewable_id) REFERENCES houses (id)');
        $this->addSql('ALTER TABLE house_upgrade_renewable ADD CONSTRAINT FK_A7055EB86BB74515 FOREIGN KEY (house_id) REFERENCES renewables (id)');
        $this->addSql('ALTER TABLE renewables ADD CONSTRAINT FK_A83CF9DF87FD23C6 FOREIGN KEY (relatedCost_id) REFERENCES build_costs (id)');
        $this->addSql('ALTER TABLE config_choices ADD CONSTRAINT FK_70D254C912469DE2 FOREIGN KEY (category_id) REFERENCES config_categories (id)');
        $this->addSql('ALTER TABLE config_choices ADD CONSTRAINT FK_70D254C987FD23C6 FOREIGN KEY (relatedCost_id) REFERENCES build_costs (id)');
        $this->addSql('ALTER TABLE config_subsidies ADD CONSTRAINT FK_8D385B7724DB0683 FOREIGN KEY (config_id) REFERENCES config_choices (id)');
        $this->addSql('ALTER TABLE config_subsidies ADD CONSTRAINT FK_8D385B77C7F0C12C FOREIGN KEY (subsidy_id) REFERENCES subsidies (id)');
        $this->addSql('ALTER TABLE subsidies ADD CONSTRAINT FK_ECEE6C4AD75064B4 FOREIGN KEY (subsidyCategory_id) REFERENCES subsidy_categories (id)');
        $this->addSql('ALTER TABLE config_transformations ADD CONSTRAINT FK_67E13D09CE0521DA FOREIGN KEY (fromConfig_id) REFERENCES config_choices (id)');
        $this->addSql('ALTER TABLE config_transformations ADD CONSTRAINT FK_67E13D0945B35FEE FOREIGN KEY (toConfig_id) REFERENCES config_choices (id)');

        // db session table
        $this->addSql('CREATE TABLE IF NOT EXISTS session (session_id varchar(255) NOT NULL, session_value text NOT NULL, session_time int(11) NOT NULL, PRIMARY KEY (session_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8');

        // default data
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/001/data_build_costs.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/001/data_config_categories.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/001/data_config_choices.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/001/data_config_transformations.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/001/data_subsidy_categories.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/001/data_subsidies.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/001/data_renewables.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/001/data_parameters.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/001/data_default_surfaces.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/001/data_default_roofs.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/001/data_default_energy.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/001/data_users.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/001/data_config_subsidies.sql'));
        $this->addSql(file_get_contents(__DIR__ . '/../Resources/db/001/data_content.sql'));
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CB52C922DE');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CB59B27F5E');
        $this->addSql('ALTER TABLE house_config DROP FOREIGN KEY FK_6737B6F524DB0683');
        $this->addSql('ALTER TABLE house_renewable DROP FOREIGN KEY FK_AFFB24264CAFE380');
        $this->addSql('ALTER TABLE house_upgrade_config DROP FOREIGN KEY FK_2F646F177D2FC74');
        $this->addSql('ALTER TABLE house_upgrade_renewable DROP FOREIGN KEY FK_A7055EB86BAB3765');
        $this->addSql('ALTER TABLE house_renewable DROP FOREIGN KEY FK_AFFB24266BB74515');
        $this->addSql('ALTER TABLE house_upgrade_renewable DROP FOREIGN KEY FK_A7055EB86BB74515');
        $this->addSql('ALTER TABLE renewables DROP FOREIGN KEY FK_A83CF9DF87FD23C6');
        $this->addSql('ALTER TABLE config_choices DROP FOREIGN KEY FK_70D254C987FD23C6');
        $this->addSql('ALTER TABLE house_config DROP FOREIGN KEY FK_6737B6F56BB74515');
        $this->addSql('ALTER TABLE house_upgrade_config DROP FOREIGN KEY FK_2F646F176BB74515');
        $this->addSql('ALTER TABLE config_subsidies DROP FOREIGN KEY FK_8D385B7724DB0683');
        $this->addSql('ALTER TABLE config_transformations DROP FOREIGN KEY FK_67E13D09CE0521DA');
        $this->addSql('ALTER TABLE config_transformations DROP FOREIGN KEY FK_67E13D0945B35FEE');
        $this->addSql('ALTER TABLE config_choices DROP FOREIGN KEY FK_70D254C912469DE2');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CBA4A802A0');
        $this->addSql('ALTER TABLE config_categories DROP FOREIGN KEY FK_62FADC4E84A0A3ED');
        $this->addSql('ALTER TABLE config_subsidies DROP FOREIGN KEY FK_8D385B77C7F0C12C');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE default_roofs');
        $this->addSql('DROP TABLE default_surfaces');
        $this->addSql('DROP TABLE houses');
        $this->addSql('DROP TABLE house_config');
        $this->addSql('DROP TABLE house_renewable');
        $this->addSql('DROP TABLE house_upgrade_config');
        $this->addSql('DROP TABLE house_upgrade_renewable');
        $this->addSql('DROP TABLE renewables');
        $this->addSql('DROP TABLE build_costs');
        $this->addSql('DROP TABLE config_choices');
        $this->addSql('DROP TABLE config_subsidies');
        $this->addSql('DROP TABLE config_categories');
        $this->addSql('DROP TABLE default_energy');
        $this->addSql('DROP TABLE content');
        $this->addSql('DROP TABLE subsidies');
        $this->addSql('DROP TABLE config_transformations');
        $this->addSql('DROP TABLE parameters');
        $this->addSql('DROP TABLE session');
    }
}
