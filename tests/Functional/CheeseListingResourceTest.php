<?php


namespace App\Tests\Functional;

use App\ApiPlatform\Test\ApiTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class CheeseListingResourceTest extends ApiTestCase {
	use ReloadDatabaseTrait;

	public function testCreateCheeseListing(){
		$client = self::createClient();
		$client->request('POST', '/api/cheeses', [
			'headers' => ['Content-Type' => 'application/json'],
			'json' => []
		]);
		$this->assertResponseStatusCodeSame(401);

		$user = new User();
		$user->setEmail('cheeselover@example.com');
		$user->setUsername('cheeselover');
		$user->setPassword('$argon2id$v=19$m=65536,t=6,p=1$uyv4C2pJWTAAkmXYCcTmBQ$82uLohVIWj6lqwgioWijAhmuaTYDnC5CIFeaZ2YS+Zg');
		$em = self::$container->get(EntityManagerInterface::class);
		$em->persist($user);
		$em->flush();
		$client->request('POST', '/login', [
			'headers' => ['Content-Type' => 'application/json'],
			'json' => [
				'email' => 'cheeselover@example.com',
				'password' => 'foo'
			]
		]);
		$this->assertResponseStatusCodeSame(204);
	}
}