<?php

namespace App\Tests\Functional\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ContactTest extends WebTestCase
{
    public function testCrudIsHere(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => 1]);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, '/admin');
        $this->assertResponseIsSuccessful();

        $crawler = $client->clickLink('Contact requests');
        $this->assertResponseIsSuccessful();

        $client->click($crawler->filter('.action-new')->link());
        $this->assertResponseIsSuccessful();

        $client->request(Request::METHOD_GET, '/admin');

        $client->click($crawler->filter('.action-edit')->link());
        $this->assertResponseIsSuccessful();
    }
}
