<?php

namespace App\Form;

use App\Entity\AttributeValue;
use App\Enum\CustomAttributeType;
use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name')
            ->add('tags', TextType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'tag-input'],
                'data' => $this->getTags($options['item']),
            ]);


        if ($options['collection'] instanceof ItemCollection) {
            foreach ($options['collection']->getCustomAttributes() as $attribute) {
                $fieldName = AttributeValue::ATTR_PREFIX . $attribute->getId();
                $builder->add(
                    $fieldName,
                    $this->getFieldType($attribute->getType()->value),
                    [
                        'label' => $attribute->getName(),
                        'mapped' => false,
                        'data' => $options['attributes'][$fieldName] ?? null,
                    ]
                );
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
            'collection' => ItemCollection::class,
            'attributes' => [],
            'item' => Item::class,
        ]);
    }

    private function getFieldType(string $attrType): string
    {
        return match ($attrType) {
            CustomAttributeType::Boolean->value => CheckboxType::class,
            CustomAttributeType::Date->value => DateType::class,
            CustomAttributeType::Integer->value => NumberType::class,
            CustomAttributeType::Text->value => TextareaType::class,
            default => TextType::class,
        };
    }

    private function getTags(Item $item): string
    {
        $tags = '';
        foreach ($item->getTags() as $tag) {
            $tags .= $tag->getName() . ' ';
        }
        return $tags;
    }

}
