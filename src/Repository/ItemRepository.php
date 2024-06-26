<?php

namespace App\Repository;

use App\Entity\AttributeValue;
use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Entity\Tag;
use App\Entity\User;
use App\Enum\CustomAttributeType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;

/**
 * @extends ServiceEntityRepository<Item>
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    public function findByUserAndCollection(User $userId, ItemCollection $collectionId): Query
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.itemCollection', 'c')
            ->innerJoin('c.user', 'u')
            ->where('u.id = :userId')
            ->andWhere('c.id = :collectionId')
            ->setParameter('userId', $userId)
            ->setParameter('collectionId', $collectionId)
            ->getQuery();
    }

    public function createCustomAttributes(Item                   $item, ItemCollection $collection, FormInterface $form,
                                           EntityManagerInterface $entityManager): void
    {
        foreach ($collection->getCustomAttributes() as $attribute) {
            $value = $form->get(AttributeValue::ATTR_PREFIX . $attribute->getId())->getData();
            if ($value !== null) {
                $itemAttribute = new AttributeValue();
                $itemAttribute->setItem($item);
                $itemAttribute->setCustomAttribute($attribute);
                $this->setCustomAttributes($attribute->getType()->value, $itemAttribute, $value);
                $entityManager->persist($itemAttribute);
            }
        }
    }

    public function editCustomAttributes(Item                   $item, ItemCollection $collection, FormInterface $form,
                                         EntityManagerInterface $entityManager): void
    {
        foreach ($item->getAttributeValues() as $attributeValue) {
            $attribute = $attributeValue->getCustomAttribute();
            $value = $form->get(AttributeValue::ATTR_PREFIX . $attribute->getId())->getData();
            $this->setCustomAttributes($attribute->getType()->value, $attributeValue, $value);
        }
    }

    public function setCustomAttributes(string $type, AttributeValue $attr, mixed $value): void
    {
        switch ($type) {
            case CustomAttributeType::Integer->value:
                $attr->setIntValue($value);
                break;
            case CustomAttributeType::Text->value:
                $attr->setTextValue($value);
                break;
            case CustomAttributeType::Boolean->value:
                $attr->setBoolValue($value);
                break;
            case CustomAttributeType::Date->value:
                $attr->setDateValue($value);
                break;
            default:
                $attr->setStringValue($value);
                break;
        }
    }

    public function findMostRecentItems(int $limit = 10): array
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function fullTextSearch(string $query): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT i.* FROM item i
            LEFT JOIN comment c ON c.item_id = i.id
            LEFT JOIN item_collection col ON col.id = i.item_collection_id
            LEFT JOIN tag_item it ON it.item_id = i.id
            LEFT JOIN tag t ON t.id = it.tag_id
            WHERE MATCH (i.name) AGAINST (:query)
            OR MATCH (c.content) AGAINST (:query)
            OR MATCH (col.name, col.description) AGAINST (:query)
            OR MATCH (t.name) AGAINST (:query)
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['query' => $query]);
        return $resultSet->fetchAllAssociative();
    }

    public function findByTag(Tag $tag)
    {
        return $this->createQueryBuilder('i')
            ->innerJoin('i.tags', 't')
            ->where('t.id = :tag')
            ->setParameter('tag', $tag->getId())
            ->getQuery()
            ->getResult();
    }
}
