<?php

namespace App\Tests\Functional;

use App\Entity\Ingredient;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IngredientTest extends WebTestCase
{
    public function testIfCreateIngredientIsSuccessful(): void
    {
        $client = static::createClient();

        // Recup urlgenerator
        $urlGenerator = $client->getContainer()->get('router');

        // Recup entity manager
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);

        $client->loginUser($user);

        // Go to ingredient creation page
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.new'));

        // Manage form
        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => "An ingredient",
            'ingredient[price]' => floatval(33)
        ]);

        $client->submit($form);

        // Manage redirect
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();
        // Manage alert box and route
        $this->assertSelectorTextContains('div.alert-success', 'New ingredient added !');

        $this->assertRouteSame('ingredient.index');
    }

    public function testIfListOfIngredientsIsSuccessful(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.index'));

        $this->assertResponseIsSuccessful();

        $this->assertRouteSame('ingredient.index');
    }

    public function testIfUpdateIngredientIsSuccessful(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);

        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);

        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('ingredient.edit', ['id' => $ingredient->getId()])
        );

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => "An ingredient 2",
            'ingredient[price]' => floatval(34)
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'Ingredient edited !');

        $this->assertRouteSame('ingredient.index');
    }

    public function testIfDeleteAnIngredientIsSuccessful(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);

        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy([
            'user' => $user
        ]);

        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('ingredient.delete', ['id' => $ingredient->getId()])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'Ingredient successfully deleted !');

        $this->assertRouteSame('ingredient.index');
    }
}
