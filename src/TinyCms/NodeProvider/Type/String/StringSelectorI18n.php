<?php

namespace TinyCms\NodeProvider\Type\String;

class StringSelectorI18n extends BaseSelector {

	protected $options;

	protected $references;

	public function hasI18n()
	{
		return true;
	}

	public function setOptions($options)
	{
		$this->options = $options;
	}

	public function getOptions()
	{
		return $this->options;
	}

	public function getReferenceType()
	{
		return 'TinyCmsCore/String';
	}

	public function setReferencesI18n($lang, $references)
	{
		$this->references[$lang] = $references;
	}

	public function getReferencesI18n($lang)
	{
		return $this->references[$lang];
	}
}
