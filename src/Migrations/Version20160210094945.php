<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160210094945 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql("UPDATE build_costs SET label = 'centrale verwarming label B (HR+ ketel)' WHERE slug='heating_hr_plus'");
        $this->addSql("UPDATE build_costs SET label = 'centrale verwarming label A (HR top)' WHERE slug='heating_hr_top'");
        $this->addSql("UPDATE build_costs SET label = 'warmtepomp label C (lucht)' WHERE slug='heat_pump_ll'");
        $this->addSql("UPDATE build_costs SET label = 'warmtepomp label A+ (bodem)' WHERE slug='heat_pump_ggb'");

        $this->addSql("UPDATE subsidies SET label = 'centrale verwarming label B (HR+ ketel)' WHERE label='condensatieketel HR+'");
        $this->addSql("UPDATE subsidies SET label = 'centrale verwarming label A (HR top)' WHERE label='condensatieketel HR top'");
        $this->addSql("UPDATE subsidies SET label = 'warmtepomp label C (lucht)' WHERE label='warmtepomp lucht'");
        $this->addSql("UPDATE subsidies SET label = 'warmtepomp label A+ (bodem)' WHERE label='warmtepomp bodem'");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {

    }
}
