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

		$this->createUserAndLogIn($client, 'cheeselover@example.com', 'foo');

		$client->request('POST', '/api/cheeses', [
			'json' => []
		]);
		$this->assertResponseStatusCodeSame(400);
	}

	public function testUpdateCheeseListing(){
		$client = self::createClient();
		$user = $this->createUser('cheeseList@example.com', 'foo');
		$cheeseListing = new CheeseListing('Block of cheedar');
		$cheeseListing->setOwner($user);
		$cheeseListing->setPrice(1000);
		$cheeseListing->setDescription('hmmm');
		$em = $this->getEntityManager();
		$em->persist($cheeseListing);
		$em->flush();
		$this->logIn($client, 'cheeseList@example.com', 'foo');
		$client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
			'json' => ['title' => 'updated']
		]);
		$this->assertResponseStatusCodeSame(200);
	}
}