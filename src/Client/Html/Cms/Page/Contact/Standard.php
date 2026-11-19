<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2026
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Cms\Page\Contact;


/**
 * Default implementation for CMS contact form.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		return '';
	}


	/**
	 * Modifies the cached body content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified body content
	 */
	public function modify( string $content, string $uid ) : string
	{
		return $this->replaceSection( $content, $this->view()->csrf()->formfield(), 'cms.page.contact.csrf' );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * Replaces the <contact> elements in the CMS content by the contact form. The
	 * form is rendered from the template only, so CMS content can't add CSRF tokens
	 * to forms posting to other routes.
	 *
	 * @param \Aimeos\Base\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	public function data( \Aimeos\Base\View\Iface $view, array &$tags = [], ?string &$expire = null ) : \Aimeos\Base\View\Iface
	{
		if( !isset( $view->pageContent ) ) {
			return parent::data( $view, $tags, $expire );
		}

		/** client/html/cms/page/template-contact
		 * Relative path to the HTML template of the page contact form client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the contact form that replaces the <contact> elements in
		 * the CMS content. The configuration string is the path to the template
		 * file relative to the templates directory (usually in client/html/templates).
		 *
		 * The CSRF token field must be enclosed by <!-- cms.page.contact.csrf -->
		 * markers so it's replaced by the current token if the page is cached.
		 *
		 * @param string Relative path to the template creating the contact form
		 * @since 2026.10
		 * @see client/html/cms/page/template-cataloglist
		 */
		$template = $this->context()->config()->get( 'client/html/cms/page/template-contact', 'cms/page/contact/body' );
		$form = null;

		$render = function() use ( $view, $template, &$form ) {
			return $form ??= $view->render( $template );
		};

		$view->pageContent = array_map( function( $content ) use ( $render ) {
			return \Aimeos\MShop\Cms\Html::replace( $content, 'contact', $render );
		}, $view->pageContent );

		return parent::data( $view, $tags, $expire );
	}


	/**
	 * Processes the input, e.g. store given values.
	 *
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables if necessary.
	 */
	public function init() : void
	{
		$view = $this->view();
		$params = $view->param( 'contact' );

		if( !( $params['url'] ?? null ) && ( $params['email'] ?? null ) && ( $params['message'] ?? null ) )
		{
			$context = $this->context();
			$config = $context->config();

			$toAddr = $config->get( 'resource/email/from-email' );
			$toName = $config->get( 'resource/email/from-name' );

			if( $toAddr )
			{
				$label = $context->locale()->getSiteItem()->getLabel();

				$context->mail()->create()
					// @phpstan-ignore-next-line
					->to( $toAddr, $toName )
					// @phpstan-ignore-next-line
					->from( $toAddr, $toName )
					// @phpstan-ignore-next-line
					->replyTo( $params['email'], $params['name'] ?? null )
					->subject( $context->translate( 'client', 'Your request' ) . ' - ' . $label )
					->text( $this->text( $params ) )
					->send();

				$info = [$context->translate( 'client', 'Message sent successfully' )];
				// @phpstan-ignore-next-line
				$view->infos = array_merge( $view->get( 'infos', [] ), $info );
			}
			else
			{
				$error = [$context->translate( 'client', 'No recipient configured' )];
				// @phpstan-ignore-next-line
				$view->errors = array_merge( $view->get( 'errors', [] ), $error );
			}
		}
	}


	/**
	 * Returns the message text
	 *
	 * @param array $params Associative list of key/value pairs
	 * @return string Message text
	 */
	protected function text( array $params ) : string
	{
		$msg = '';

		foreach( $params as $key => $val ) {
			$msg .= $key . ': ' . $val . "\n";
		}

		return $msg;
	}
}
