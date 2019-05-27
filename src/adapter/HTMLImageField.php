<?php

namespace PasswordlessLogin\adapter;

use OOUI\HtmlSnippet;
use OOUI\Widget;
use HTMLFormField;
use MWException;

/**
 * A field that shows an image, either by loading it from an URL or as a data uri.
 */
class HTMLImageField extends HTMLFormField {
	/**
	 * @var null|string
	 */
	private $source;
	/**
	 * @var null|string
	 */
	private $dataUri;
	/**
	 * @param array $info
	 *   In adition to the usual HTMLFormField parameters, this can take the following fields:
	 *   - source: The source of the image, if it should be loaded from an URL.
	 *   - data-uri: The data URI if the image data is provided to the class.
	 *   One of source or data-uri is required.
	 */
	public function __construct( $info ) {
		$info['nodata'] = true;
		if ( !$info['source'] && !$info['data-uri'] ) {
			throw new MWException( 'At least one of source or data-uri is required, none given.' );
		}

		$this->source = $info['source'];
		$this->dataUri = $info['data-uri'];

		parent::__construct( $info );
	}

	public function getInputHTML( $value ) {
		return '<img src="' . $this->getSource() . '" alt="' . $this->getLabel() . '" />';
	}

	public function getInputOOUI( $value ) {
		return new Widget( [
			'content' => new HtmlSnippet( $this->getInputHTML( $value ) ),
		] );
	}

	protected function needsLabel() {
		return false;
	}

	private function getSource() {
		if ($this->source) {
			return $this->source;
		}
		return $this->dataUri;
	}
}
