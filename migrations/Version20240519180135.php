<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240519180135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(40) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE custom_attribute (id INT AUTO_INCREMENT NOT NULL, item_collection_id INT NOT NULL, name VARCHAR(80) NOT NULL, type VARCHAR(20) NOT NULL, INDEX IDX_B040985DBDE5FE26 (item_collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, item_collection_id INT NOT NULL, name VARCHAR(100) NOT NULL, INDEX IDX_1F1B251EBDE5FE26 (item_collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_collection (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, category_id INT NOT NULL, name VARCHAR(40) NOT NULL, description LONGTEXT NOT NULL, image_url VARCHAR(255) DEFAULT NULL, INDEX IDX_41FC4D38A76ED395 (user_id), INDEX IDX_41FC4D3812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_item (tag_id INT NOT NULL, item_id INT NOT NULL, INDEX IDX_F1149AA8BAD26311 (tag_id), INDEX IDX_F1149AA8126F525E (item_id), PRIMARY KEY(tag_id, item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE custom_attribute ADD CONSTRAINT FK_B040985DBDE5FE26 FOREIGN KEY (item_collection_id) REFERENCES item_collection (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EBDE5FE26 FOREIGN KEY (item_collection_id) REFERENCES item_collection (id)');
        $this->addSql('ALTER TABLE item_collection ADD CONSTRAINT FK_41FC4D38A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE item_collection ADD CONSTRAINT FK_41FC4D3812469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE tag_item ADD CONSTRAINT FK_F1149AA8BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_item ADD CONSTRAINT FK_F1149AA8126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE custom_attribute DROP FOREIGN KEY FK_B040985DBDE5FE26');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EBDE5FE26');
        $this->addSql('ALTER TABLE item_collection DROP FOREIGN KEY FK_41FC4D38A76ED395');
        $this->addSql('ALTER TABLE item_collection DROP FOREIGN KEY FK_41FC4D3812469DE2');
        $this->addSql('ALTER TABLE tag_item DROP FOREIGN KEY FK_F1149AA8BAD26311');
        $this->addSql('ALTER TABLE tag_item DROP FOREIGN KEY FK_F1149AA8126F525E');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE custom_attribute');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE item_collection');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_item');
    }
}
