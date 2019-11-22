<?php


namespace App\Tests\Functional;

use App\ApiPlatform\Test\ApiTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use App\Test\CustomApiTestCase;

class CheeseListingResourceTest extends CustomApiTestCase {
	use ReloadDatabaseTrait;

	public function testCreateCheeseListing(){
		$client = self::createClient();
		$client->request('POST', '/api/cheeses', [
			'headers' => ['Content-Type' => 'application/json'],
			'json' => []
		]);
		$this->assertResponseStatusCodeSame(401);

		$this->createUserAndLogIn($client, 'cheeselover@example.com', 'foo');

		$client->request('POST', '/api/cheeses', [
			'headers' => ['Content-Type' => 'application/json'],
			'json' => []
		]);
		$this->assertResponseStatusCodeSame(400);
	}
}