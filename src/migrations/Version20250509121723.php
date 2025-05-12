<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250509121723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tenders (id INT AUTO_INCREMENT NOT NULL, external_code BIGINT NOT NULL, number VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, date_update DATETIME NOT NULL, status_id INT DEFAULT NULL, INDEX IDX_D52D2EEE6BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tenders ADD CONSTRAINT FK_D52D2EEE6BF700BD FOREIGN KEY (status_id) REFERENCES status (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE tenders DROP FOREIGN KEY FK_D52D2EEE6BF700BD
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE status
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tenders
        SQL);
    }
}
