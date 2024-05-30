<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240529215317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attribute_value (id INT AUTO_INCREMENT NOT NULL, custom_attribute_id INT NOT NULL, item_id INT NOT NULL, int_value INT DEFAULT NULL, string_value VARCHAR(255) DEFAULT NULL, text_value LONGTEXT DEFAULT NULL, bool_value TINYINT(1) DEFAULT NULL, date_value DATE DEFAULT NULL, INDEX IDX_FE4FBB82CD6139E1 (custom_attribute_id), INDEX IDX_FE4FBB82126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attribute_value ADD CONSTRAINT FK_FE4FBB82CD6139E1 FOREIGN KEY (custom_attribute_id) REFERENCES custom_attribute (id)');
        $this->addSql('ALTER TABLE attribute_value ADD CONSTRAINT FK_FE4FBB82126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attribute_value DROP FOREIGN KEY FK_FE4FBB82CD6139E1');
        $this->addSql('ALTER TABLE attribute_value DROP FOREIGN KEY FK_FE4FBB82126F525E');
        $this->addSql('DROP TABLE attribute_value');
    }
}
