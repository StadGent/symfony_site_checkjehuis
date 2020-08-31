<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190111113335 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE default_surfaces CHANGE livingarea living_area DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE content CHANGE candeactivate can_deactivate TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE fos_user DROP locked, DROP expired, DROP expires_at, DROP credentials_expired, DROP credentials_expire_at, CHANGE username username VARCHAR(180) NOT NULL, CHANGE username_canonical username_canonical VARCHAR(180) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL, CHANGE email_canonical email_canonical VARCHAR(180) NOT NULL, CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479C05FB297 ON fos_user (confirmation_token)');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CB2D2255D3');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CB52C922DE');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CB59B27F5E');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CB7036B2E8');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CBA4A802A0');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CBBE6B401D');
        $this->addSql('DROP INDEX IDX_95D7F5CBA4A802A0 ON houses');
        $this->addSql('DROP INDEX IDX_95D7F5CB52C922DE ON houses');
        $this->addSql('DROP INDEX IDX_95D7F5CB7036B2E8 ON houses');
        $this->addSql('DROP INDEX IDX_95D7F5CB59B27F5E ON houses');
        $this->addSql('DROP INDEX IDX_95D7F5CB2D2255D3 ON houses');
        $this->addSql('DROP INDEX IDX_95D7F5CBBE6B401D ON houses');
        $this->addSql('ALTER TABLE houses '
          . 'CHANGE defaultEnergy_id default_energy_id INT DEFAULT NULL, '
          . 'CHANGE defaultSurface_id default_surface_id INT DEFAULT NULL, '
          . 'CHANGE defaultRoof_id default_roof_id INT DEFAULT NULL, '
          . 'change defaultRoofIfFlat_id default_roof_if_flat_id INT DEFAULT NULL, '
          . 'CHANGE extraConfigRoof_id extra_config_roof_id INT DEFAULT NULL, '
          . 'CHANGE extraUpgradeRoof_id extra_upgrade_roof_id INT DEFAULT NULL, '
          . 'CHANGE buildingType building_type VARCHAR(255) NOT NULL, '
          . 'CHANGE roofType roof_type VARCHAR(255) NOT NULL, '
          . 'CHANGE consumptionGas consumption_gas DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE consumptionElec consumption_elec DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE surfaceLivingArea surface_living_area DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE surfaceFloor surface_floor DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE surfaceFacade surface_facade DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE surfaceWindow surface_window DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE surfaceRoof surface_roof DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE surfaceRoofExtra surface_roof_extra DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE electricHeating electric_heating TINYINT(1) NOT NULL, '
          . 'CHANGE hasWindroof has_windroof TINYINT(1) NOT NULL, '
          . 'CHANGE placeWindroof place_windroof TINYINT(1) NOT NULL, '
          . 'CHANGE lastknownroute last_known_route VARCHAR(255) DEFAULT NULL, '
          . 'CHANGE lastupdate last_update DATETIME NOT NULL, '
          . 'CHANGE solarpanelskwhpiek solar_panels_kwhpiek DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CB9FBFDD8A FOREIGN KEY (default_energy_id) REFERENCES default_energy (id)');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CB8232A157 FOREIGN KEY (default_surface_id) REFERENCES default_surfaces (id)');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CB7193A6D7 FOREIGN KEY (default_roof_id) REFERENCES default_roofs (id)');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CBFE6EE1AC FOREIGN KEY (default_roof_if_flat_id) REFERENCES default_roofs (id)');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CB62F56DDA FOREIGN KEY (extra_config_roof_id) REFERENCES config_choices (id)');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CBBB663391 FOREIGN KEY (extra_upgrade_roof_id) REFERENCES config_choices (id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CB9FBFDD8A ON houses (default_energy_id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CB8232A157 ON houses (default_surface_id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CB7193A6D7 ON houses (default_roof_id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CBFE6EE1AC ON houses (default_roof_if_flat_id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CB62F56DDA ON houses (extra_config_roof_id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CBBB663391 ON houses (extra_upgrade_roof_id)');
        $this->addSql('ALTER TABLE config_choices '
          . 'CHANGE possibleCurrent possible_current TINYINT(1) NOT NULL, '
          . 'CHANGE possibleUpgrade possible_upgrade TINYINT(1) NOT NULL, '
          . 'CHANGE defaultuptoyear default_up_to_year VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE config_categories CHANGE hasInverseMatrix has_inverse_matrix TINYINT(1) NOT NULL, CHANGE fromActual from_actual TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE default_energy CHANGE maxyear max_year VARCHAR(255) NOT NULL, CHANGE electricheating electric_heating DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE subsidies DROP FOREIGN KEY FK_ECEE6C4AD75064B4');
        $this->addSql('DROP INDEX IDX_ECEE6C4AD75064B4 ON subsidies');
        $this->addSql('ALTER TABLE subsidies CHANGE subsidycategory_id subsidy_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subsidies ADD CONSTRAINT FK_ECEE6C4AD60716DC FOREIGN KEY (subsidy_category_id) REFERENCES subsidy_categories (id)');
        $this->addSql('CREATE INDEX IDX_ECEE6C4AD60716DC ON subsidies (subsidy_category_id)');
        $this->addSql('ALTER TABLE config_transformations DROP FOREIGN KEY FK_67E13D0945B35FEE');
        $this->addSql('ALTER TABLE config_transformations DROP FOREIGN KEY FK_67E13D09CE0521DA');
        $this->addSql('DROP INDEX IDX_67E13D0945B35FEE ON config_transformations');
        $this->addSql('DROP INDEX IDX_67E13D09CE0521DA ON config_transformations');
        $this->addSql('ALTER TABLE config_transformations '
          . 'CHANGE fromConfig_id from_config_id INT DEFAULT NULL, '
          . 'CHANGE toConfig_id to_config_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE config_transformations ADD CONSTRAINT FK_67E13D0934479339 FOREIGN KEY (from_config_id) REFERENCES config_choices (id)');
        $this->addSql('ALTER TABLE config_transformations ADD CONSTRAINT FK_67E13D091578D1F2 FOREIGN KEY (to_config_id) REFERENCES config_choices (id)');
        $this->addSql('CREATE INDEX IDX_67E13D0934479339 ON config_transformations (from_config_id)');
        $this->addSql('CREATE INDEX IDX_67E13D091578D1F2 ON config_transformations (to_config_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE config_categories '
          . 'CHANGE has_inverse_matrix hasInverseMatrix TINYINT(1) NOT NULL, '
          . 'CHANGE from_actual fromActual TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE config_choices '
          . 'CHANGE possible_current possibleCurrent TINYINT(1) NOT NULL, '
          . 'CHANGE possible_upgrade possibleUpgrade TINYINT(1) NOT NULL, '
          . 'CHANGE default_up_to_year defaultUpToYear VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE config_transformations DROP FOREIGN KEY FK_67E13D0934479339');
        $this->addSql('ALTER TABLE config_transformations DROP FOREIGN KEY FK_67E13D091578D1F2');
        $this->addSql('DROP INDEX IDX_67E13D0934479339 ON config_transformations');
        $this->addSql('DROP INDEX IDX_67E13D091578D1F2 ON config_transformations');
        $this->addSql('ALTER TABLE config_transformations '
          . 'ADD fromConfig_id INT DEFAULT NULL, '
          . 'ADD toConfig_id INT DEFAULT NULL, '
          . 'DROP from_config_id, '
          . 'DROP to_config_id');
        $this->addSql('ALTER TABLE config_transformations ADD CONSTRAINT FK_67E13D0945B35FEE FOREIGN KEY (toConfig_id) REFERENCES config_choices (id)');
        $this->addSql('ALTER TABLE config_transformations ADD CONSTRAINT FK_67E13D09CE0521DA FOREIGN KEY (fromConfig_id) REFERENCES config_choices (id)');
        $this->addSql('CREATE INDEX IDX_67E13D0945B35FEE ON config_transformations (toConfig_id)');
        $this->addSql('CREATE INDEX IDX_67E13D09CE0521DA ON config_transformations (fromConfig_id)');
        $this->addSql('ALTER TABLE content CHANGE can_deactivate canDeactivate TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE default_energy CHANGE max_year maxYear VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE electric_heating electricHeating DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE default_surfaces CHANGE living_area livingArea DOUBLE PRECISION NOT NULL');
        $this->addSql('DROP INDEX UNIQ_957A6479C05FB297 ON fos_user');
        $this->addSql('ALTER TABLE fos_user ADD locked TINYINT(1) NOT NULL, ADD expired TINYINT(1) NOT NULL, ADD expires_at DATETIME DEFAULT NULL, ADD credentials_expired TINYINT(1) NOT NULL, ADD credentials_expire_at DATETIME DEFAULT NULL, CHANGE username username VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE username_canonical username_canonical VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE email email VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE email_canonical email_canonical VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE salt salt VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CB9FBFDD8A');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CB8232A157');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CB7193A6D7');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CBFE6EE1AC');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CB62F56DDA');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CBBB663391');
        $this->addSql('DROP INDEX IDX_95D7F5CB9FBFDD8A ON houses');
        $this->addSql('DROP INDEX IDX_95D7F5CB8232A157 ON houses');
        $this->addSql('DROP INDEX IDX_95D7F5CB7193A6D7 ON houses');
        $this->addSql('DROP INDEX IDX_95D7F5CBFE6EE1AC ON houses');
        $this->addSql('DROP INDEX IDX_95D7F5CB62F56DDA ON houses');
        $this->addSql('DROP INDEX IDX_95D7F5CBBB663391 ON houses');
        $this->addSql('ALTER TABLE houses '
          . 'CHANGE building_type buildingType VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, '
          . 'CHANGE roof_type roofType VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, '
          . 'CHANGE consumption_gas consumptionGas DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE consumption_elec consumptionElec DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE surface_floor surfaceFloor DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE surface_facade surfaceFacade DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE surface_window surfaceWindow DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE surface_roof surfaceRoof DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE surface_roof_extra surfaceRoofExtra DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE electric_heating electricHeating TINYINT(1) NOT NULL, '
          . 'CHANGE has_windroof hasWindroof TINYINT(1) NOT NULL, '
          . 'CHANGE place_windroof placeWindroof TINYINT(1) NOT NULL, '
          . 'CHANGE default_energy_id defaultEnergy_id INT DEFAULT NULL, '
          . 'CHANGE default_surface_id defaultSurface_id INT DEFAULT NULL, '
          . 'CHANGE default_roof_id defaultRoof_id INT DEFAULT NULL, '
          . 'CHANGE surface_living_area surfaceLivingArea DOUBLE PRECISION DEFAULT NULL, '
          . 'CHANGE extra_config_roof_id extraConfigRoof_id INT DEFAULT NULL, '
          . 'CHANGE extra_upgrade_roof_id extraUpgradeRoof_id INT DEFAULT NULL, '
          . 'CHANGE default_roof_if_flat_id defaultRoofIfFlat_id INT DEFAULT NULL, '
          . 'CHANGE last_known_route lastKnownRoute VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, '
          . 'CHANGE last_update lastUpdate DATETIME NOT NULL, '
          . 'CHANGE solar_panels_kwhpiek solarPanelsKWHPiek DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CB2D2255D3 FOREIGN KEY (extraConfigRoof_id) REFERENCES config_choices (id)');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CB52C922DE FOREIGN KEY (defaultRoof_id) REFERENCES default_roofs (id)');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CB59B27F5E FOREIGN KEY (defaultSurface_id) REFERENCES default_surfaces (id)');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CB7036B2E8 FOREIGN KEY (extraUpgradeRoof_id) REFERENCES config_choices (id)');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CBA4A802A0 FOREIGN KEY (defaultEnergy_id) REFERENCES default_energy (id)');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CBBE6B401D FOREIGN KEY (defaultRoofIfFlat_id) REFERENCES default_roofs (id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CBA4A802A0 ON houses (defaultEnergy_id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CB52C922DE ON houses (defaultRoof_id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CB7036B2E8 ON houses (extraUpgradeRoof_id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CB59B27F5E ON houses (defaultSurface_id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CB2D2255D3 ON houses (extraConfigRoof_id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CBBE6B401D ON houses (defaultRoofIfFlat_id)');
        $this->addSql('ALTER TABLE subsidies DROP FOREIGN KEY FK_ECEE6C4AD60716DC');
        $this->addSql('DROP INDEX IDX_ECEE6C4AD60716DC ON subsidies');
        $this->addSql('ALTER TABLE subsidies CHANGE subsidy_category_id subsidyCategory_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subsidies ADD CONSTRAINT FK_ECEE6C4AD75064B4 FOREIGN KEY (subsidyCategory_id) REFERENCES subsidy_categories (id)');
        $this->addSql('CREATE INDEX IDX_ECEE6C4AD75064B4 ON subsidies (subsidyCategory_id)');
    }
}
