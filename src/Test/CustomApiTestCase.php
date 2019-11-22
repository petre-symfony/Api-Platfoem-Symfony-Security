<?php


namespace App\Test;

use App\ApiPlatform\Test\ApiTestCase;
use App\ApiPlatform\Test\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CustomApiTestCase extends ApiTestCase{
	protected function createUser(string $email, string $password){
		$user = new User();
		$user->setEmail($email);
		$user->setUsername(substr($email, 0, strpos($email, '@')));
		
		$encoded = self::$container
			->get(UserPasswordEncoderInterface::class)
			->encodePassword($user, $password);
		$user->setPassword($encoded);

		$em = self::$container->get(EntityManagerInterface::class);
		$em->persist($user);
		$em->flush();

		return $user;
	}

	protected function logIn(Client $client, string $email, string $password){
		$client->request('POST', '/login', [
			'headers' => ['Content-Type' => 'application/json'],
			'json' => [
				'email' => $email,
				'password' => $password
			]
		]);
		$this->assertResponseStatusCodeSame(204);
	}

	protected function createUserAndLogIn(Client $client, string $email, string $password):User{
		$user = $this->createUser($email, $password);
		$this->logIn($client, $email, $password);
		return $user;
	}
}