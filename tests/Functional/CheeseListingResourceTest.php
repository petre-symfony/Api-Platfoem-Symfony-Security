<?php


namespace App\Tests\Functional;

use App\ApiPlatform\Test\ApiTestCase;
use App\Entity\User;
use App\Entity\CheeseListing;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use App\Test\CustomApiTestCase;


class CheeseListingResourceTest extends CustomApiTestCase {
	use ReloadDatabaseTrait;

	public function testCreateCheeseListing(){
		$client = self::createClient();
		$client->request('POST', '/api/cheeses', [
			'json' => []
		]);
		$this->assertResponseStatusCodeSame(401);

		$authenticatedUser = $this->createUserAndLogIn($client, 'cheeselover@example.com', 'foo');
		$otherUser = $this->createUser('gugu@gmail.com', 'foo');

		$cheeseData = [
			'title' => 'Mystery cheese... kinda green',
			'description' => 'What mysteries does it hold?',
			'price' => 50000
		];

		$client->request('POST', '/api/cheeses', [
			'json' => $cheeseData
		]);
		$this->assertResponseStatusCodeSame(201);

		$client->request('POST', '/api/cheeses', [
			'json' => $cheeseData + ['owner' => '/api/users/'.$otherUser->getId()]
		]);

		$this->assertResponseStatusCodeSame(400, 'not passing the correct owner');

		$client->request('POST', '/api/cheeses', [
			'json' => $cheeseData + ['owner' => '/api/users/'.$authenticatedUser->getId()]
		]);

		$this->assertResponseStatusCodeSame(201);
	}

	public function testUpdateCheeseListing(){
		$client = self::createClient();
		$user1 = $this->createUser('user1@example.com', 'foo');
		$user2 = $this->createUser('user2@example.com', 'foo');

		$cheeseListing = new CheeseListing('Block of cheedar');
		$cheeseListing->setOwner($user1);
		$cheeseListing->setPrice(1000);
		$cheeseListing->setDescription('hmmm');

		$em = $this->getEntityManager();
		$em->persist($cheeseListing);
		$em->flush();

		$this->logIn($client, 'user2@example.com', 'foo');
		$client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
			'json' => ['title' => 'updated', 'owner' => '/api/users/'.$user2->getId()]
		]);
		$this->assertResponseStatusCodeSame(403);

		//var_dump($client->getResponse()->getContent(false));

		$this->logIn($client, 'user1@example.com', 'foo');
		$client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
			'json' => ['title' => 'updated']
		]);
		$this->assertResponseStatusCodeSame(200);
	}

	public function testGetCheeseListingCollection(){
		$client = self::createClient();
		$user = $this->createUser('gogu@gmail.com', 'gogu');

		$cheeseListing1 = new CheeseListing('cheedar');
		$cheeseListing1->setOwner($user);
		$cheeseListing1->setPrice(3000);
		$cheeseListing1->setDescription('cheese');

		$cheeseListing2 = new CheeseListing('feta');
		$cheeseListing2->setOwner($user);
		$cheeseListing2->setPrice(4000);
		$cheeseListing2->setDescription('cheese');

		$cheeseListing3 = new CheeseListing('telemea');
		$cheeseListing3->setOwner($user);
		$cheeseListing3->setPrice(3000);
		$cheeseListing3->setDescription('cheese');

		$em = $this->getEntityManager();
		$em->persist($cheeseListing1);
		$em->persist($cheeseListing2);
		$em->persist($cheeseListing3);
		$em->flush();
		
		$client->request('GET', '/api/cheeses');
		$this->assertJsonContains(['hydra:totalItems' => 3]);
	}
}