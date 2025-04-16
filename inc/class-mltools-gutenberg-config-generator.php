<?php

class MLTools_Gutenberg_Config_Generator {

	const META_BOX_ID             = 'wpml-meta-box-id';
	const META_BOX_TITLE          = 'WPML XML Configuration';
	const NON_TRANSLATABLE_BLOCKS = [];
	const WILDCARD_ATTRIBUTES     = [
		'desktop',
	];

	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'addMetaBox' ] );
	}

	/**
	 * Adds a meta box to the post and page editor.
	 */
	public function addMetaBox() {
		if ( ! use_block_editor_for_post( get_the_ID() ) ) {
			return;
		}

		add_meta_box(
			self::META_BOX_ID,
			esc_html__( self::META_BOX_TITLE, 'wpml-compatibility-test-tools' ),
			[ $this, 'renderMetaBoxHtml' ],
			[ 'post', 'page' ]
		);
	}

	/**
	 * Renders the HTML content for the meta box.
	 *
	 * @param WP_Post $post The current post object.
	 */
	public function renderMetaBoxHtml( $post ) {
		$content    = get_post_field( 'post_content', $post->ID );
		$xmlContent = $this->generateXmlFromContent( $content );

		$this->renderMetaBoxHeader();
		$this->renderXmlTextarea( $xmlContent );
		$this->renderDebugInformation( $content );
	}

	/**
	 * Renders the header for the meta box.
	 */
	private function renderMetaBoxHeader() {
		echo '<h3>' . esc_html__( 'WPML: Gutenberg Blocks', 'wpml-compatibility-test-tools' ) . '</h3>';
		echo '<p>' . wp_kses_post( __( 'XML automatically generated for the blocks and block attributes from this page. <strong>Please review before using it.</strong>', 'wpml-compatibility-test-tools' ) ) . '</p>';
		echo '<p style="margin-top: 10px;">' . wp_kses_post( __( 'For instructions on how to use and implement it, please check the following links:', 'wpml-compatibility-test-tools' ) ) . '</p>';
		echo '<p>- <a href="https://wpml.org/documentation/support/language-configuration-files/" target="_blank">' . esc_html__( 'WPML Language Configuration File', 'wpml-compatibility-test-tools' ) . '</a></p>';
		echo '<p>- <a href="https://wpml.org/documentation/support/language-configuration-files/make-custom-gutenberg-blocks-translatable/" target="_blank">' . esc_html__( 'Make Custom Gutenberg Blocks Translatable', 'wpml-compatibility-test-tools' ) . '</a></p>';
	}

	/**
	 * Renders the XML content in a textarea.
	 *
	 * @param string $xmlContent The XML content to display.
	 */
	private function renderXmlTextarea( $xmlContent ) {
		echo '<textarea style="width:100%; min-height: 200px; margin-top: 10px;">';
		echo htmlspecialchars( $xmlContent );
		echo '</textarea>';
	}

	/**
	 * Renders debug information for the Gutenberg blocks.
	 *
	 * @param string $content The post content.
	 */
	private function renderDebugInformation( $content ) {
		$sections = $this->getDebugSections( $content );

		echo '<h4>' . esc_html__( 'Gutenberg Blocks - Debug Information', 'wpml-compatibility-test-tools' ) . '</h4>';

		foreach ( $sections as $section ) {
			$this->renderDebugSection( $section );
		}
	}

	/**
	 * Gets the debug sections for the Gutenberg blocks.
	 *
	 * @param string $content The post content.
	 *
	 * @return array The debug sections.
	 */
	private function getDebugSections( $content ) {
		return [
			[
				'title'       => esc_html__( 'Post Content', 'wpml-compatibility-test-tools' ),
				'description' => esc_html__( 'RAW content from the post_content column of the current post.', 'wpml-compatibility-test-tools' ),
				'content'     => htmlspecialchars_decode( $content ),
			],
			[
				'title'       => esc_html__( 'Parse Blocks from post_content', 'wpml-compatibility-test-tools' ),
				'description' => esc_html__( 'Result of parse_blocks() applied to the page content (JSON format).', 'wpml-compatibility-test-tools' ),
				'content'     => htmlspecialchars( json_encode( parse_blocks( $content ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) ),
			],
		];
	}

	/**
	 * Renders a single debug section.
	 *
	 * @param array $section The section data.
	 */
	private function renderDebugSection( $section ) {
		$html  = '<details style="border: 1px solid #ddd; padding: 20px; margin-bottom: 10px;">';
		$html .= '<summary style="font-weight: bold; font-size: 14px; cursor: pointer;">' . esc_html__( $section['title'], 'wpml-compatibility-test-tools' ) . '</summary>';
		$html .= '<p>' . esc_html__( $section['description'], 'wpml-compatibility-test-tools' ) . '</p>';
		$html .= '<textarea style="width:100%; min-height: 100px; margin-top: 10px;">';
		$html .= $section['content'];
		$html .= '</textarea>';
		$html .= '</details>';

		echo $html;
	}

	/**
	 * Returns a list of non-translatable Gutenberg blocks.
	 *
	 * @return array
	 */
	public function getNonTranslatableBlocksList() {
		return apply_filters( 'mltools_gutenberg_non_translatable_blocks_list', self::NON_TRANSLATABLE_BLOCKS );
	}

	/**
	 * Returns a list of wildcard attributes.
	 *
	 * @return array
	 */
	public function getWildcardAttributesList() {
		return apply_filters( 'mltools_gutenberg_wildcart_attributes_list', self::WILDCARD_ATTRIBUTES );
	}

	/**
	 * Parses the content into an array of Gutenberg blocks.
	 *
	 * @param string $content The post content.
	 *
	 * @return array
	 */
	public function parseContentToBlocks( $content ) {
		return parse_blocks( $content );
	}

	/**
	 * Filters and returns an array of Gutenberg blocks.
	 *
	 * @param string $content The post content.
	 *
	 * @return array
	 */
	public function getFilteredBlocksArray( $content ) {
		$blocks         = $this->parseContentToBlocks( $content );
		$filteredBlocks = $this->filterBlocksArray( $blocks );

		return (array) $filteredBlocks;
	}

	/**
	 * Filters the blocks array to remove duplicates and organize blocks.
	 *
	 * @param array $blocks The array of blocks.
	 * @param array $filteredBlocks The array of filtered blocks.
	 *
	 * @return array
	 */
	public function filterBlocksArray( $blocks, $filteredBlocks = [] ) {

		foreach ( $blocks as $block ) {
			if ( isset( $block['blockName'] ) ) {

				if ( ! array_key_exists( $block['blockName'], $filteredBlocks ) ) {
					$filteredBlocks[ $block['blockName'] ] = $block;
				} elseif ( array_key_exists( $block['blockName'], $filteredBlocks ) ) {
					$filteredBlocks[ $block['blockName'] ] = array_merge(
						$filteredBlocks[ $block['blockName'] ],
						$block
					);
				}
				if ( isset( $block['innerBlocks'] ) && ! empty( $block['innerBlocks'] ) ) {

					$innerBlocks = $this->filterBlocksArray( $block['innerBlocks'], $filteredBlocks );

					$filteredBlocks = array_merge(
						$filteredBlocks,
						$innerBlocks
					);

					unset( $filteredBlocks[ $block['blockName'] ]['innerBlocks'] );

				}
			}
		}

		return (array) $filteredBlocks;
	}

	/**
	 * Generates XML from the post content.
	 *
	 * @param string $content The post content.
	 *
	 * @return string
	 */
	private function generateXmlFromContent( $content ) {
		try {
			$blocks                = $this->getFilteredBlocksArray( $content );
			$nonTranslatableBlocks = $this->getNonTranslatableBlocksList();

			$xml             = new SimpleXMLElement( '<wpml-config></wpml-config>' );
			$gutenbergBlocks = $xml->addChild( 'gutenberg-blocks' );

			foreach ( $blocks as $block ) {
				$this->generateXmlForBlock( $gutenbergBlocks, $block, $nonTranslatableBlocks );
			}

			$dom               = dom_import_simplexml( $xml )->ownerDocument;
			$dom->formatOutput = true;
			return $dom->saveXML( $dom->documentElement, LIBXML_NOXMLDECL );
		} catch ( Exception $e ) {
			error_log( 'Error generating XML: ' . $e->getMessage() );
			return '';
		}
	}

	/**
	 * Generates XML for a single Gutenberg block.
	 *
	 * @param SimpleXMLElement $xmlElement The XML element to append to.
	 * @param array            $block The block data.
	 * @param array            $nonTranslatableBlocks The list of non-translatable blocks.
	 */
	private function generateXmlForBlock( $xmlElement, $block, $nonTranslatableBlocks ) {
		if ( ! isset( $block['blockName'] ) || empty( $block['blockName'] ) ) {
			return;
		}

		$blockType = $block['blockName'];
		$translate = in_array( $block['blockName'], $nonTranslatableBlocks ) ? '0' : '1';

		$blockElement = $xmlElement->addChild( 'gutenberg-block' );
		$blockElement->addAttribute( 'type', $blockType );
		$blockElement->addAttribute( 'translate', $translate );

		if ( $translate === '1' && ! empty( $block['innerHTML'] ) ) {
			$this->addXpathElements( $blockElement, $block );
		}

		if ( ! empty( $block['attrs'] && ! in_array( $block['blockName'], $nonTranslatableBlocks ) ) ) {
			$this->generateXmlForAttributes( $blockElement, $block['attrs'] );
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			foreach ( $block['innerBlocks'] as $innerBlock ) {
				$this->generateXmlForBlock( $xmlElement, $innerBlock, $nonTranslatableBlocks );
			}
		}
	}

	/**
	 * Generates XML for block attributes.
	 *
	 * @param SimpleXMLElement $xmlElement The XML element to append to.
	 * @param array            $attrs The block attributes.
	 */
	private function generateXmlForAttributes( $xmlElement, $attrs ) {
		$wildcardAttributesList = $this->getWildcardAttributesList();

		foreach ( $attrs as $key => $value ) {
			if ( is_array( $value ) ) {
				if ( in_array( $key, $wildcardAttributesList ) ) {
					$key = '*';
				}
				$keyElement = $xmlElement->addChild( 'key' );
				$keyElement->addAttribute( 'name', $key );
				$this->generateXmlForAttributes( $keyElement, $value );
			} else {
				$keyElement = $xmlElement->addChild( 'key' );
				$keyElement->addAttribute( 'name', $key );

				if ( strpos( $key, 'url' ) !== false ||
					 strpos( $key, 'link' ) !== false ||
					 strpos( $key, 'href' ) !== false ) {
					$keyElement->addAttribute( 'type', 'link' );
				}
			}
		}
	}

	/**
	 * Adds xpath elements to the block element based on the HTML content.
	 *
	 * @param SimpleXMLElement $blockElement The block XML element.
	 * @param array            $block The block data.
	 */
	private function addXpathElements( $blockElement, $block ) {
		if ( empty( $block['innerHTML'] ) ) {
			return;
		}

		$xpaths = $this->analyzeHtmlForXpaths( $block['innerHTML'] );

		foreach ( $xpaths as $xpath ) {
			$child = $blockElement->addChild( 'xpath', $xpath );

			if ( strpos( $xpath, '@href' ) !== false ) {
				$child->addAttribute( 'type', 'link' );
			}
		}
	}

	/**
	 * Analyzes HTML content to determine appropriate xpaths.
	 *
	 * @param string $html The HTML content.
	 *
	 * @return array Array of xpath expressions.
	 */
	private function analyzeHtmlForXpaths( $html ) {
		$xpaths = [];

		if ( empty( trim( $html ) ) ) {
			return $xpaths;
		}

		$doc = new DOMDocument();
		@$doc->loadHTML( '<div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );

		$xpath = new DOMXPath( $doc );

		$textNodes = $this->findTextNodes( $doc->documentElement );

		foreach ( $textNodes as $node ) {
			if ( $node->nodeType === XML_TEXT_NODE ) {
				$text = trim( $node->nodeValue );
				if ( $this->isTranslatableText( $text ) ) {
					$parent = $node->parentNode;
					if ( $parent !== null ) {
						$xpathExpression = $this->generateUniqueXPath( $parent );
						if ( ! empty( $xpathExpression ) ) {
							$xpaths[] = $xpathExpression;
						}
					}
				}
			}
		}

		$links = $xpath->query( '//a[@href]' );
		foreach ( $links as $link ) {
			$xpaths[] = $this->generateUniqueXPath( $link ) . '/@href';
		}

		$attrMap = [
			'img'      => [ 'alt', 'title' ],
			'input'    => [ 'placeholder', 'value' ],
			'textarea' => [ 'placeholder' ],
			'button'   => [ 'value' ],
			'meta'     => [ 'content' ],
		];

		foreach ( $attrMap as $tag => $attributes ) {
			$elements = $doc->getElementsByTagName( $tag );
			foreach ( $elements as $element ) {
				foreach ( $attributes as $attribute ) {
					if ( $element->hasAttribute( $attribute ) && $this->isTranslatableText( $element->getAttribute( $attribute ) ) ) {
						$xpaths[] = $this->generateUniqueXPath( $element ) . '/@' . $attribute;
					}
				}
			}
		}

		return array_unique( $xpaths );
	}

	/**
	 * Recursively finds text nodes within an element.
	 *
	 * @param DOMNode $node The node to check.
	 *
	 * @return array Array of text nodes.
	 */
	private function findTextNodes( $node ) {
		$textNodes = [];

		if ( $node->nodeType === XML_TEXT_NODE ) {
			$text = trim( $node->nodeValue );
			if ( $this->isTranslatableText( $text ) ) {
				$textNodes[] = $node;
			}
		}

		if ( $node->hasChildNodes() ) {
			foreach ( $node->childNodes as $child ) {
				$textNodes = array_merge( $textNodes, $this->findTextNodes( $child ) );
			}
		}

		return $textNodes;
	}

	/**
	 * Generates a unique XPath for a DOM element.
	 *
	 * @param DOMElement $element The DOM element.
	 *
	 * @return string The XPath expression.
	 */
	private function generateUniqueXPath( $element ) {
		if ( $element->nodeType !== XML_ELEMENT_NODE ) {
			return '';
		}

		$nodePath = $element->nodeName;

		if ( $element->hasAttribute( 'class' ) ) {
			$classes         = explode( ' ', $element->getAttribute( 'class' ) );
			$filteredClasses = array_filter( $classes, 'trim' );

			if ( ! empty( $filteredClasses ) ) {
				$selectedClass = $this->selectMostUniqueClass( $filteredClasses );
				$nodePath     .= '[contains(@class, "' . $selectedClass . '")]';
			}
		} else {
			$parent = $element->parentNode;
			if ( $parent && $parent->nodeType === XML_ELEMENT_NODE ) {
				$siblings = 0;
				$position = 0;

				foreach ( $parent->childNodes as $i => $sibling ) {
					if ( $sibling->nodeType === XML_ELEMENT_NODE && $sibling->nodeName === $element->nodeName ) {
						$siblings++;
						if ( $sibling === $element ) {
							$position = $siblings;
						}
					}
				}

				if ( $siblings > 1 ) {
					$nodePath .= '[' . $position . ']';
				}
			}
		}

		if ( $element->childNodes->length === 1 && $element->firstChild->nodeType === XML_TEXT_NODE ) {
			return '//' . $nodePath;
		}

		return '//' . $nodePath;
	}

	/**
	 * Selects the most unique class from a list of classes.
	 *
	 * @param array $classes Array of class names.
	 *
	 * @return string The selected class name.
	 */
	private function selectMostUniqueClass( $classes ) {
		foreach ( $classes as $class ) {
			if ( strpos( $class, 'id' ) !== false ||
				 strpos( $class, 'ID' ) !== false ||
				 preg_match( '/[A-Za-z0-9]{5,}/', $class ) ) {
				return $class;
			}
		}

		return $this->getLongestClass( $classes );
	}

	/**
	 * Gets the longest class name from a list of classes.
	 *
	 * This method sorts the array of class names by length (descending)
	 * and returns the first (longest) element. This is used as a fallback
	 * strategy when we can't find classes with specific patterns.
	 *
	 * @param array $classes Array of class names to analyze.
	 *
	 * @return string The class name with the longest string length or empty string if array is empty.
	 */
	private function getLongestClass( $classes ) {
		if ( empty( $classes ) ) {
			return '';
		}

		usort(
			$classes,
			function( $a, $b ) {
				return strlen( $b ) - strlen( $a );
			}
		);

		return reset( $classes );
	}

	/**
	 * @param string $text The text to check.
	 *
	 * @return bool Whether the text is translatable.
	 */
	private function isTranslatableText( $text ) {
		$text = trim( $text );
		return ! empty( $text ) && ! is_numeric( $text );
	}
}

new MLTools_Gutenberg_Config_Generator();
