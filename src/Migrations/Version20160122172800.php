<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160122172800 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE build_costs SET ordering = ordering + 2 WHERE ordering > 17");

        $this->addSql("INSERT INTO build_costs (id, ordering, slug, label, value, unit) VALUES
                        (22, 7, 'heating_ap', 'centrale verwarming label A+', 0, ''),
                        (23, 7, 'heating_app', 'centrale verwarming label A++', 0, '')");

        $this->addSql("UPDATE config_choices SET ordering = ordering + 2 WHERE ordering > 5 AND category_id = 6");

        $this->addSql("INSERT INTO config_choices (id, category_id, ordering, label, `default`, defaultUpToYear, possibleCurrent, possibleUpgrade, relatedCost_id) VALUES
                        (45, '6', '6', 'centrale verwarming label A+', '0', '', '1', '1', 21),
                        (46, '6', '7', 'centrale verwarming label A++', '0', '', '1', '1', 21)");

        $this->addSql("INSERT INTO subsidies (id, slug, subsidyCategory_id, label, value, multiplier, max) VALUES
                        (101, 'heating_ap', 1, 'centrale verwarming label A+', '0', '', '0'),
                        (102, 'heating_ap', 2, 'centrale verwarming label A+', '0', '', '0'),
                        (103, 'heating_ap', 3, 'centrale verwarming label A+', '0', '', '0'),
                        (104, 'heating_ap', 4, 'centrale verwarming label A+', '0', '', '0'),
                        (105, 'heating_ap', 5, 'centrale verwarming label A+', '0', '', '0'),
                        (106, 'heating_app', 1, 'centrale verwarming label A++', '0', '', '0'),
                        (107, 'heating_app', 2, 'centrale verwarming label A++', '0', '', '0'),
                        (108, 'heating_app', 3, 'centrale verwarming label A++', '0', '', '0'),
                        (109, 'heating_app', 4, 'centrale verwarming label A++', '0', '', '0'),
                        (110, 'heating_app', 5, 'centrale verwarming label A++', '0', '', '0')");

        $this->addSql("INSERT INTO config_subsidies (subsidy_id, config_id) VALUES
                        (101, 45),
                        (102, 45),
                        (103, 45),
                        (104, 45),
                        (105, 45),
                        (106, 46),
                        (107, 46),
                        (108, 46),
                        (109, 46),
                        (110, 46)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
