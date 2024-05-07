<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240506150920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("insert into book_format (id, title, description, comment) values (1, 'eBook', 'Eliminate the unavoidable complexity of object-oriented designs. The innovative data-oriented programming paradigm makes your systems less complex by making it simpler to access and manipulate data.', null)");
        $this->addSql("insert into book_format (id, title, description, comment) values (2, 'print + eBook', 'Data-Oriented Programming is a one-of-a-kind guide that introduces the data-oriented paradigm.', 'This groundbreaking approach represents data')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('delete from ');
    }
}
