<?php

namespace App\Repository;

use App\Entity\AttributeValue;
use App\Entity\Item;
use App\Entity\ItemCollection;
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


    //    /**
    //     * @return Item[] Returns an array of Item objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Item
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
