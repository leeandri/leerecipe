<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ContactTest extends WebTestCase
{
    public function testIfSubmitContactFormIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Contact form');

        //Retrieve form
        $submitButton = $crawler->selectButton('Submit my request');
        $form = $submitButton->form();

        $form["contact[fullName]"] = "Jean Dupont";
        $form["contact[email]"] = "jd@leerecipe.mg";
        $form["contact[subject]"] = "Test";
        $form["contact[message]"] = "Test";

        //Submit form
        $client->submit($form);

        //Check HTTP status
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        //Check mail sending
        $this->assertEmailCount(1);

        $client->followRedirect();

        //Check message presence
        $this->assertSelectorTextContains(
            'div.alert.alert-success.mt-4',
            'Your request was submitted successfully !'
        );
    }
}
