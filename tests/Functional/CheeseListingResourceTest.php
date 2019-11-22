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

		$this->createUser('cheeselover@example.com', '$argon2id$v=19$m=65536,t=6,p=1$uyv4C2pJWTAAkmXYCcTmBQ$82uLohVIWj6lqwgioWijAhmuaTYDnC5CIFeaZ2YS+Zg');
		$this->logIn($client, 'cheeselover@example.com', 'foo');
	}
}