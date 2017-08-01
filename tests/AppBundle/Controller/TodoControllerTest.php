<?php

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TodoControllerTest extends WebTestCase
{
    /**
     * @var $client
     */
    private $client = null;

    /**
     * Setup for tests
     */
    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * Testing if authenticated user can access page with al tasks
     */
    public function testAllWithAuthentication()
    {
        $this->logInRandomValidUser();
        $crawler = $this->client->request('GET', '/todo');

        $this->assertEquals('To-do list', $crawler->filter('.panel-heading')->text(), 'Panel Heading has wrong text,
                            it should has \'To-do list\'' );
    }

    /**
     * Testing if nonauthenticated user is redirected to login page
     */
    public function testAllWithoutAuthentication()
    {
        $this->client->request('GET', '/todo');

        $this->assertTrue($this->client->getResponse()->isRedirect('http://localhost/login'), 'User is not redirected, but he should');
    }

    /**
     * Checking if user get 404 exception if he try to access non existing taks
     */
    public function testIfUserCantSeeNonExistingTask()
    {
        $this->logInRandomValidUser();
        $this->client->request('GET', '/todo/details/666');

        $this->assertTrue($this->client->getResponse()->isNotFound(), 'User can access non existing task, but should not.');
    }

    /**
     * Method to authenticate random user
     */
    private function logInRandomValidUser()
    {
        //https://stackoverflow.com/a/30555103/6912075
        $session = $this->client->getContainer()->get('session');

        $userManager = $this->client->getContainer()->get('fos_user.user_manager');

        $user=$userManager->findUserByUsername('tester');
        if (!$user) {
            $user = $userManager->createUser();

            $user->setEmail('test@example.com');
            $user->setUsername('tester');
            $user->setPlainPassword('foo');
            $user->setEnabled(true);
            $user->addRole('ROLE_USER');

            $userManager->updateUser($user);
        }

        $firewall = 'main';
        $token = new UsernamePasswordToken($user, null, $firewall, array('ROLE_SUPER_ADMIN'));

        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}