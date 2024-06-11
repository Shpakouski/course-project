<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;

/**
 * @extends ServiceEntityRepository<Tag>
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function findByTerm(string $term): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.name LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    }

    public function addTags(FormInterface $form, EntityManagerInterface $entityManager, Item $item): void
    {

        $tagNames = explode(' ', $form->get('tags')->getData());
        $tags = [];
        foreach ($tagNames as $tagName) {
            $tagName = trim($tagName);
            if (empty($tagName)) {
                continue;
            }
            $tag = $entityManager->getRepository(Tag::class)->findOneBy(['name' => $tagName]);
            if (!$tag) {
                $tag = new Tag();
                $tag->setName($tagName);
                $entityManager->persist($tag);
            }
            $tags[] = $tag;
        }
        $item->getTags()->clear();
        foreach ($tags as $tag) {
            $item->addTag($tag);
        }
        $entityManager->persist($item);
    }

    public function findAllWithItemCounts()
    {
        return $this->createQueryBuilder('t')
            ->select('t, COUNT(i.id) AS item_count')
            ->leftJoin('t.item', 'i')
            ->groupBy('t.id')
            ->getQuery()
            ->getResult();
    }
}
