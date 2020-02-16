<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190629173542 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE action (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', type VARCHAR(255) NOT NULL, config LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', language VARCHAR(5) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chapter (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, position INT NOT NULL, language VARCHAR(5) NOT NULL, UNIQUE INDEX chapter_position_unique (language, position), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE step (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', lesson_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', action_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', position INT NOT NULL, text LONGTEXT NOT NULL, INDEX IDX_43B9FE3CCDF80196 (lesson_id), UNIQUE INDEX UNIQ_43B9FE3C9D32F035 (action_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', chapter_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, position INT NOT NULL, INDEX IDX_F87474F3579F4768 (chapter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', login VARCHAR(255) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, api_id INT NOT NULL, provider VARCHAR(255) NOT NULL, access_token VARCHAR(255) DEFAULT NULL, roles VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history_container (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', history_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', image VARCHAR(255) NOT NULL, args LONGTEXT NOT NULL, INDEX IDX_51EBBFF91E058452 (history_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_27BA704BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE step ADD CONSTRAINT FK_43B9FE3CCDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE step ADD CONSTRAINT FK_43B9FE3C9D32F035 FOREIGN KEY (action_id) REFERENCES action (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id)');
        $this->addSql('ALTER TABLE history_container ADD CONSTRAINT FK_51EBBFF91E058452 FOREIGN KEY (history_id) REFERENCES history (id)');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE step DROP FOREIGN KEY FK_43B9FE3C9D32F035');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3579F4768');
        $this->addSql('ALTER TABLE step DROP FOREIGN KEY FK_43B9FE3CCDF80196');
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704BA76ED395');
        $this->addSql('ALTER TABLE history_container DROP FOREIGN KEY FK_51EBBFF91E058452');
        $this->addSql('DROP TABLE action');
        $this->addSql('DROP TABLE chapter');
        $this->addSql('DROP TABLE step');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE history_container');
        $this->addSql('DROP TABLE history');
    }
}
