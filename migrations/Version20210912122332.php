<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210912122332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apartment CHANGE bed bed INT DEFAULT NULL, CHANGE bathroom bathroom INT DEFAULT NULL, CHANGE living_room living_room INT DEFAULT NULL');
        $this->addSql('ALTER TABLE house CHANGE floor floor INT DEFAULT NULL, CHANGE bathroom bathroom INT DEFAULT NULL, CHANGE living_room living_room INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room CHANGE ac ac TINYINT(1) DEFAULT NULL, CHANGE balcony balcony TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE apartment CHANGE bed bed INT NOT NULL, CHANGE bathroom bathroom INT NOT NULL, CHANGE living_room living_room INT NOT NULL');
        $this->addSql('ALTER TABLE house CHANGE floor floor INT NOT NULL, CHANGE bathroom bathroom INT NOT NULL, CHANGE living_room living_room INT NOT NULL');
        $this->addSql('ALTER TABLE room CHANGE ac ac TINYINT(1) NOT NULL, CHANGE balcony balcony TINYINT(1) NOT NULL');
    }
}
