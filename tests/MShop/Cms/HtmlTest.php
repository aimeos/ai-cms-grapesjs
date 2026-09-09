<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */

namespace Aimeos\MShop\Cms;


class HtmlTest extends \PHPUnit\Framework\TestCase
{
	public function testHtmlSanitizesObfuscatedFormAction()
	{
		$allow = ['form' => ['/']];
		$content = '<form action="/"><button formaction="java&#9;script:alert(1)">Run</button></form>';

		$this->assertSame( '<form action="/"><button>Run</button></form>', trim( Html::sanitize( $content, $allow ) ) );
	}


	public function testHtmlRejectsActiveConfiguredElements()
	{
		$allow = [
			'base' => true,
			'link' => true,
			'meta' => true,
			'script' => ['https://evil.example/'],
			'style' => true,
		];
		$content = '<base href="/x/"><link rel="stylesheet" href="https://evil.example/x.css">'
			. '<meta http-equiv="refresh" content="0;url=https://evil.example/">'
			. '<style>body{display:none}</style><script src="https://evil.example/x.js"></script><p>Safe</p>';

		$this->assertSame( '<p>Safe</p>', trim( Html::sanitize( $content, $allow ) ) );
	}


	public function testHtmlRemovesScriptMetadataAndPreservesName()
	{
		$html = '<div data-gjs-script="parent.xss=1" data-gjs-name="Row">Safe</div>';

		$this->assertSame( '<div data-gjs-name="Row">Safe</div>', trim( Html::sanitize( $html ) ) );
	}


	public function testHtmlRemovesCombinedWidgetAttributes()
	{
		$html = '<div data-counturl="/counts" data-infiniteurl="/page2" data-url="/suggest" data-rmurl="/unpin" data-options=\'{"indexIndicatorSep":"<img src=x onerror=alert(1)>"}\' data-custom="safe">Text</div>';
		$this->assertSame( '<div data-custom="safe">Text</div>', trim( Html::sanitize( $html ) ) );
	}


	public function testNormalizesAllowConfiguration()
	{
		$config = new \Aimeos\Base\Config\PHPArray( [
			'admin' => ['cms' => ['allow' => [
				'iframe' => ['https://example.org/', false, 17],
				'form' => true,
				'input' => false,
				'invalid' => 'yes',
				0 => true,
			]]],
		] );

		$this->assertSame( [
			'iframe' => ['https://example.org/'],
			'form' => true,
			'input' => false,
		], Html::getAllow( $config ) );
	}


	public function testRemovesStorefrontEndpointsFromRichText()
	{
		foreach( ['data-counturl', 'DATA-COUNTURL', 'data-infiniteurl', 'DATA-INFINITEURL',
			'data-url', 'DATA-URL', 'data-rmurl', 'DATA-RMURL'] as $name ) {
			foreach( ['data:text/html,unsafe', '/shop/next', 'https://example.org/next'] as $url ) {
				$this->assertSame( '<div class="catalog-list-items" data-custom="safe">Text</div>', trim( Html::sanitize(
					'<div class="catalog-list-items" ' . $name . '="' . $url . '" data-custom="safe">Text</div>'
				) ) );
			}
		}
	}


	public function testRemovesActiveEditorMetadata()
	{
		foreach( [
			'data-gjs-script="parent.xss=1"',
			'DATA-GJS-SCRIPT="parent.xss=1"',
			'data-gjs-attributes=\'{"onerror":"parent.xss=1"}\'',
			'data-gjs-components=\'[{"script":"parent.xss=1"}]\'',
			'data-gjs-content="&lt;img src=x onerror=parent.xss=1&gt;"',
			'data-gjs-type="script" data-gjs-tagname="script"',
			'data-gjs-script-export="parent.xss=1" data-gjs-unknown="value"',
			'data-gjs-name="&lt;img src=x onerror=parent.xss=1&gt;"',
			'data-gjs-name=\'{"name":"bad"}\' data-gjs-droppable=\'[".col"]\'',
			'data-gjs-draggable=".row\" data-gjs-name="bad&#10;name"',
		] as $attributes ) {
			$this->assertSame( '<div><p>Safe</p></div>', trim( Html::sanitize( '<div ' . $attributes . '><p>Safe</p></div>' ) ) );
		}
	}


	public function testRemovesLightboxOptionsFromRichText()
	{
		foreach( ['data-options', 'DATA-OPTIONS'] as $name ) {
			$html = '<div class="catalog-detail" ' . $name . '=\'{"indexIndicatorSep":"<img src=x onerror=alert(1)>"}\' data-custom="safe">Gallery</div>';
			$this->assertSame( '<div class="catalog-detail" data-custom="safe">Gallery</div>', trim( Html::sanitize( $html ) ) );
		}
	}


	public function testPreservesPassiveMetadataAndMarkup()
	{
		$html = '<div data-gjs-name="Überschrift 2" data-gjs-draggable=".row" data-gjs-droppable="false" class="col" data-custom="value"><p>Safe &amp; sound</p></div>';
		$this->assertSame( $html, trim( Html::sanitize( $html ) ) );
		$this->assertSame( $html, trim( Html::sanitize( str_replace( '<p>', '<p data-gjs-script="parent.xss=1">', $html ) ) ) );
	}


	public function testPreservesHtmlPolicyAndAllowedEmbeds()
	{
		$html = '<form action="/" data-gjs-script="parent.xss=1"><button formaction="java&#9;script:alert(1)">Run</button></form>';
		$this->assertSame( '<form action="/"><button>Run</button></form>', trim( Html::sanitize( $html, ['form' => ['/']] ) ) );

		$html = '<div data-gjs-script="parent.xss=1"><iframe src="https://www.youtube.com/embed/test"></iframe></div>';
		$result = Html::sanitize( $html, ['iframe' => ['https://www.youtube.com/embed/']] );
		$this->assertStringContainsString( 'src="https://www.youtube.com/embed/test"', $result );
		$this->assertStringContainsString( 'sandbox=', $result );
		$this->assertStringNotContainsString( 'data-gjs-script', $result );
	}


	public function testRejectsActiveDocumentElementsWhenAllowed()
	{
		$html = '<base href="/x/"><link rel="stylesheet" href="https://evil.example/x.css">'
			. '<meta http-equiv="refresh" content="0;url=https://evil.example/">'
			. '<style>body{display:none}</style><script src="https://evil.example/x.js"></script>'
			. '<iframe src="https://www.youtube.com/embed/test"></iframe>';
		$result = Html::sanitize( $html, [
			'base' => true,
			'link' => true,
			'meta' => true,
			'script' => ['https://evil.example/'],
			'style' => true,
			'iframe' => ['https://www.youtube.com/embed/'],
		] );

		foreach( ['<base', '<link', '<meta', '<script', '<style'] as $element ) {
			$this->assertStringNotContainsString( $element, $result );
		}

		$this->assertStringContainsString( 'src="https://www.youtube.com/embed/test"', $result );
		$this->assertStringContainsString( 'sandbox=', $result );
	}
}
