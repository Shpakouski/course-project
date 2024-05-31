<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240531210211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE FULLTEXT INDEX comment_fulltext_idx ON comment (content)');
        $this->addSql('CREATE FULLTEXT INDEX item_fulltext_idx ON item (name)');
        $this->addSql('CREATE FULLTEXT INDEX collection_fulltext_idx ON item_collection (name, description)');
        $this->addSql('CREATE FULLTEXT INDEX tag_fulltext_idx ON tag (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX collection_fulltext_idx ON item_collection');
        $this->addSql('DROP INDEX comment_fulltext_idx ON comment');
        $this->addSql('DROP INDEX tag_fulltext_idx ON tag');
        $this->addSql('DROP INDEX item_fulltext_idx ON item');
    }
}
