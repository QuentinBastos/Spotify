<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class SearchType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (isset($options['music']) && $options['music']) {
            $builder
                ->add('search', TextType::class, [
                    'label' => $this->translator->trans('search.track.label'),
                    'required' => true,
                    'attr' => [
                        'placeholder' => $this->translator->trans('search.track.placeholder'),
                        'class' => 'form-control w-100 me-3',
                    ],
                ])
                ->add('save', SubmitType::class, [
                    'label' => $this->translator->trans('search.track.button'),
                    'attr' => [
                        'class' => 'btn btn-primary',
                    ],
                ]);
        } else {
            $builder
                ->add('search', TextType::class, [
                    'label' => $this->translator->trans('search.artist.label'),
                    'required' => true,
                    'attr' => [
                        'placeholder' => $this->translator->trans('search.artist.placeholder'),
                        'class' => 'form-control w-100 me-3',
                    ],
                ])
                ->add('save', SubmitType::class, [
                    'label' => $this->translator->trans('search.artist.button'),
                    'attr' => [
                        'class' => 'btn btn-primary',
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'music' => false,
        ]);
    }
}