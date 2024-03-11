<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2024
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds demo records to product tables.
 */
class DemoAddCmsData extends MShopAddDataAbstract
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopAddTypeDataCms', 'DemoAddTypeData'];
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function before() : array
	{
		return ['DemoRebuildIndex'];
	}


	/**
	 * Insert product data.
	 */
	public function up()
	{
		$context = $this->context();
		$value = $context->config()->get( 'setup/default/demo', '' );

		if( $value === '' ) {
			return;
		}


		$this->info( 'Processing CMS demo data', 'vv' );

		$domains = ['media', 'text'];
		$manager = \Aimeos\MShop::create( $context, 'cms' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '=~', 'cms.label', 'Demo ' ) );
		$pages = $manager->search( $search, $domains );

		foreach( $pages as $item )
		{
			foreach( $domains as $domain ) {
				$item->deleteListItems( $item->getListItems( $domain, null, null, false ), true );
			}
		}

		$manager->delete( $pages );


		if( $value === '1' ) {
			$this->addDemoData();
		}
	}


	/**
	 * Adds the demo data to the database.
	 *
	 * @throws \Aimeos\MShop\Exception If the file isn't found
	 */
	protected function addDemoData()
	{
		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'demo-cms.php';

		if( ( $data = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for CMS domain', $path ) );
		}

		$data = $this->replaceIds( $data );
		$manager = \Aimeos\MShop::create( $this->context(), 'cms' );

		foreach( $data as $entry )
		{
			try
			{
				$manager->find( $entry['cms.url'] );
			}
			catch( \Aimeos\MShop\Exception $e )
			{
				$item = $manager->create()->fromArray( $entry );
				$this->addRefItems( $item, $entry );
				$manager->save( $item );
			}
		}
	}


	/**
	 * Replaces the IDs in the demo data with the actual ones
	 *
	 * @param array $data Associative list of CMS demo data
	 * @return array Modfied CMS demo data
	 */
	protected function replaceIds( array $data ) : array
	{
		$manager = \Aimeos\MShop::create( $this->context(), 'catalog' );
		$filter = $manager->filter()->add( 'catalog.code', '=~', 'demo-' );

		$map = [];
		foreach( $manager->search( $filter ) as $id => $item ) {
			$map[$item->getCode()] = $id;
		}

		foreach( $data as $pos => $entry )
		{
			foreach( $entry['text'] ?? [] as $idx => $text )
			{
				$content = $text['text.content'] ?? '';

				foreach( ['2' => 'demo-best', '3' => 'demo-new', '4' => 'demo-deals'] as $id => $code )
				{
					if( $newId = $map[$code] ?? null ) {
						$content = str_replace( 'catid=\"' . $id . '\"', 'catid=\"' . $newId . '\"', $content );
					}
				}

				$data[$pos]['text'][$idx]['text.content'] = $content;
			}
		}

		return $data;
	}
}
