<?php

namespace NodePoint\Core\Storage\PDO\Library;

use NodePoint\Core\Library\EntityInterface;
use NodePoint\Core\Library\EntityFieldInterface;
use NodePoint\Core\Library\EntityTypeInterface;
use NodePoint\Core\Storage\Library\EntityManagerInterface;
use NodePoint\Core\Storage\Library\EntityStorageProxyInterface;

class EntityStorageProxy implements EntityStorageProxyInterface {

	/*
	 * @var NodePoint\Core\Storage\Library\EntityManagerInterface
	 */
	protected $em;

	/*
	 * @var NodePoint\Core\Library\EntityInterface
	 */
	protected $entity;

	/*
	 * @var array of string with fieldNames
	 */
	protected $updateFieldNames;

	/*
	 * @var array of string with language codes
	 */
	protected $loadedLanguages;

	/*
	 * Constructor
	 */
	public function __construct(EntityManagerInterface $em, EntityInterface $entity)
	{
		$this->em = $em;
		$this->entity = $entity;
		$this->updateFieldNames = null;
		$this->loadedLanguages = null;
	}

	/*
	 * @return NodePoint\Core\Storage\Library\EntityManagerInterface
	 */
	public function getEntityManager()
	{
		return $this->em;
	}

	/*
	 * @return NodePoint\Core\Library\EntityInterface
	 */
	public function getEntity()
	{
		return $this->entity;
	}

	/*
	 * @return boolean true if entity has been updated
	 */
	public function hasUpdate()
	{
		return (!empty($this->updateFieldNames));
	}

	/*
	 * @return array of string
	 */
	public function getUpdateFieldNames()
	{
		return array_keys($this->updateFieldNames);
	}	

	/*
	 * @param $fieldName string
	 */
	public function onUpdateField($fieldName)
	{
		if (null == $this->updateFieldNames)
		{
			$this->updateFieldNames = array();
			$this->em->update($this->entity);
		}
		if (empty($this->updateFieldNames[$fieldName]))
		{
			$this->updateFieldNames[$fieldName] = true;
		}
	}

	/*
	 * @param $fieldName string
	 */
	public function hasUpdateField($fieldName)
	{
		return (!empty($this->updateFieldNames[$fieldName]));
	}

	/*
	 * All fields need an update
	 */
	public function updateAllFields()
	{
		$type = $this->entity->_type();
		$fieldNames = $type->getFieldNames();
		if (!empty($fieldNames))
		{
			foreach ($fieldNames as $fieldName)
			{
				if (!$type->getFieldInfo($fieldName)->isReadOnly())
				{
					$this->onUpdateField($fieldName);
				}
			}
		}
	}

	/*
	 * Reset any update flags
	 */
	public function resetUpdate()
	{
		$this->updateFieldNames = null;
	}	

	/*
	 * Maintain list of all languages 
	 * already be loaded from storage
	 *
	 * @param $lang mixed string or array of strings
	 */
	public function addLoadedLanguage($lang)
	{
		if (null === $this->loadedLanguages)
		{
			$this->loadedLanguages = array();
		}
		if (is_array($lang))
		{
			foreach ($lang as $langItem)
			{
				$this->loadedLanguages[] = $langItem;
			}
		}
		else 
		{
			$this->loadedLanguages[] = $lang;
		}
	}

	/*
	 * @return array of strings with language codes
	 */
	public function getLoadedLanguages()
	{
		return $this->loadedLanguages;
	}

	/*
	 * @param $field NodePoint\Core\Library\EntityFieldInterface
	 * @return boolean
	 */
	public function loadField(EntityFieldInterface $field)
	{
		$lazyLoadInfo = $field->getLazyLoadInfo();
		if (null === $lazyLoadInfo)
		{
			return false;
		}
		$entityId = $lazyLoadInfo->entityId;
		$entityTypeName = $lazyLoadInfo->typeName;
		$fieldRepository = $this->em->getRepository($entityTypeName);
		if (null === $fieldRepository)
		{
			// TODO: Exception: no repository for this entity type available
			return false;
		}
		$lang = $this->getLoadedLanguages();
		$entity = $fieldRepository->find($entityId, $lang);
		if (null == $entity)
		{
			// TODO: Exception: lazy loading of entity failed
			return false;
		}
		$field->setValue($fieldRepository->find($entityId, $lang));
		$field->setLazyLoadInfo(null);
		return true;
	}
}
