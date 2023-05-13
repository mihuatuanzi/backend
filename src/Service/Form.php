<?php

namespace App\Service;

use App\Config\FormFieldType;
use App\Entity\FormField;
use App\Entity\FormFieldValidator;
use App\Entity\FormValidator;
use App\Entity\User;
use App\Exception\StructuredException;
use App\Repository\FormFieldRepository;
use App\Repository\FormFieldValidatorRepository;
use App\Repository\FormRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\ParameterBag;

readonly class Form
{
    public function __construct(
        private FormRepository $formRepository,
        private FormFieldRepository $fieldRepository,
        private FormFieldValidatorRepository $fieldValidatorRepository
    )
    {
    }

    public function bindFormFields(array $fields, \App\Entity\Form $form, ?FormValidator $formValidator): void
    {
        $fieldEntities = $this->fieldRepository->findBy([
            'id' => array_column($fields, 'id'),
            'form' => $form
        ]);

        $fieldValidatorIds = [];
        foreach ($fields as $field) {
            if (!empty($field['field_validator']['id'])) {
                $fieldValidatorIds[] = $field['field_validator']['id'];
            }
        }

        $_fieldValidatorEntities = $this->fieldValidatorRepository->findBy([
            'id' => $fieldValidatorIds
        ]);
        $fieldValidatorEntities = [];
        foreach ($_fieldValidatorEntities as $fieldValidatorEntity) {
            $fieldValidatorEntities[$fieldValidatorEntity->getId()] = $fieldValidatorEntity;
        }

        $fieldEntities = new ArrayCollection($fieldEntities);
        foreach ($fields as $index => $field) {
            $fieldBag = new ParameterBag($field);
            $id = $fieldBag->get('id');
            $fieldEntity = $fieldEntities->findFirst(fn($_, FormField $e) => $id && $e->getId() == $id);
            if (!$fieldEntity) {
                $fieldEntity = new FormField();
            }
            $fieldEntity->loadFromParameterBag($fieldBag)
                ->setOrderNumber($index)
                ->setForm($form);

            if ($formValidator && ($validator = $fieldBag->get('field_validator'))) {
                $fieldValidatorEntity = $fieldValidatorEntities[$validator['id'] ?? null] ?? new FormFieldValidator();
                $fieldValidatorEntity->setType($validator['type'] ?? null);
                $fieldValidatorEntity->setRule($validator['rule'] ?? null);
                $fieldValidatorEntity->setOrderNumber($index);
                $fieldValidatorEntity->setUpdatedAt(new DateTime());
                $fieldValidatorEntity->setFormField($fieldEntity);
                $fieldValidatorEntity->setFormValidator($formValidator);
                $fieldEntity->addFormFieldValidator($fieldValidatorEntity);
            }

            $form->addFormField($fieldEntity);
        }
    }

    public function makeFormByRequest(ParameterBag $request, User $user): \App\Entity\Form
    {
        if ($formId = $request->get('id')) {
            if (!$form = $this->formRepository->findOneBy(['id' => $formId])) {
                throw new StructuredException(['_violations' => ['找不到表单资源']], 404);
            }
            if ($form->getUser()->getId() !== $user->getId()) {
                throw new StructuredException(['_violations' => ['无法在此转让表单']], 403);
            }
        } else {
            $form = new \App\Entity\Form();
        }

        $form->setUser($user);
        $form->setTitle($request->get('title'));
        $form->setDetail($request->get('detail'));
        $form->setCreatedAt(new DateTimeImmutable());
        $form->setUpdatedAt(new DateTime());

        return $form;
    }

    public function makeFormFieldByRequest(ParameterBag $request, \App\Entity\Form $form, int $orderNumber): FormField
    {
        if ($id = $request->get('id')) {
            if (!$field = $this->fieldRepository->findOneBy(['id' => $id])) {
                throw new StructuredException(['_violations' => ['找不到表单字段资源']], 404);
            }
            if ($field->getForm()->getId() !== $form->getId()) {
                throw new StructuredException(['_violations' => ['无法在此转让表单项']], 403);
            }
        } else {
            $field = new FormField();
        }

        $field->setForm($form);
        $field->setLabel($request->get('label'));
        $field->setDetail($request->get('detail'));
        $field->setType(FormFieldType::from($request->get('type')));
        $field->setAnnotation($request->get('annotation'));
        $field->setOrderNumber($orderNumber);
        return $field;
    }
}
