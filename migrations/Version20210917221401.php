<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

use App\DataFixtures\BookFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210917221401 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getDescription(): string
    {
        return 'Fill the books with fixtures';
    }

    public function up(Schema $schema): void
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $fixtures = new BookFixture();
        $fixtures->load($em);

    }

    public function down(Schema $schema): void
    {

        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('TRUNCATE TABLE book_translation');
        $this->addSql('TRUNCATE TABLE book');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
        // this down() migration is auto-generated, please modify it to your needs

    }
}
