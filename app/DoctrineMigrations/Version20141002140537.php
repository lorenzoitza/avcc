<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141002140537 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9F1C37890');
        $this->addSql('DROP INDEX IDX_1483A5E9F1C37890 ON users');
        $this->addSql('ALTER TABLE users CHANGE organization_id_id organizations_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E986288A55 FOREIGN KEY (organizations_id) REFERENCES organizations (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E986288A55 ON users (organizations_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E986288A55');
        $this->addSql('DROP INDEX IDX_1483A5E986288A55 ON users');
        $this->addSql('ALTER TABLE users CHANGE organizations_id organization_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9F1C37890 FOREIGN KEY (organization_id_id) REFERENCES organizations (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E9F1C37890 ON users (organization_id_id)');
    }
}
