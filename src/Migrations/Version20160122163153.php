<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160122163153 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE build_costs SET ordering = ordering + 1 WHERE ordering > 6");

        $this->addSql("INSERT INTO build_costs (id, ordering, slug, label, value, unit) VALUES
                        (21, 7, 'facade_inner', 'binnengevelisolatie', 0, 'm²')");

        $this->addSql("INSERT INTO config_choices (id, category_id, ordering, label, `default`, defaultUpToYear, possibleCurrent, possibleUpgrade, relatedCost_id) VALUES
                        (44, '2', '5', 'geïsoleerd met binnengevelisolatie', '0', '', '1', '1', 21)");

        $this->addSql("INSERT INTO subsidies (id, slug, subsidyCategory_id, label, value, multiplier, max) VALUES
                        (96, 'facade_inner', 1, 'binnengevelisolatie', '0', 'surface', '0'),
                        (97, 'facade_inner', 2, 'binnengevelisolatie', '0', 'surface', '0'),
                        (98, 'facade_inner', 3, 'binnengevelisolatie', '0', 'surface', '0'),
                        (99, 'facade_inner', 4, 'binnengevelisolatie', '0', 'surface', '0'),
                        (100, 'facade_inner', 5, 'binnengevelisolatie', '0', 'surface', '0')");

        $this->addSql("INSERT INTO config_subsidies (subsidy_id, config_id) VALUES
                        (96, 44),
                        (97, 44),
                        (98, 44),
                        (99, 44),
                        (100, 44)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
