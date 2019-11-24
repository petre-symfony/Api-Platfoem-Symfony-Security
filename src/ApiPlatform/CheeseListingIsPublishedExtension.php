<?php


namespace App\ApiPlatform;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\CheeseListing;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class CheeseListingIsPublishedExtension implements QueryCollectionExtensionInterface {
	/**
	 * @var Security
	 */
	private $security;

	public function __construct(Security $security){
		$this->security = $security;
	}

	public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null){
		if($resourceClass !== CheeseListing::class){
			return;
		}

		if($this->security->isGranted('ROLE_ADMIN')){
			return;
		}

		$rootAlias = $queryBuilder->getRootAlias()[0];
		$queryBuilder->andWhere(sprintf('%s.isPublished = :isPublished', $rootAlias))
			->setParameter('isPublished', true);
	}

}