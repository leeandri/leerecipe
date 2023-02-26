<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $button = $crawler->selectLink('Learn');
        $this->assertEquals(1, count($button));

        $recipes = $crawler->filter('.recipes .d-flex.flex-wrap.justify-content-between');
        $this->assertEquals(1, count($recipes));

        $this->assertSelectorTextContains('h1', 'Welcome');
    }
}
