<?php
/*
 *  $Id: ApiTest.php 637 2012-05-01 19:58:47Z k42b3.x@googlemail.com $
 *
 * amun
 * A social content managment system based on the psx framework. For
 * the current version and informations visit <http://amun.phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of amun. amun is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * amun is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with amun. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Amun\Api;

use Amun\DataFactory;
use AmunService\Oauth;
use PSX\Sql\Condition;

/**
 * Amun_Api_System_ApiTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://amun.phpsx.org
 * @category   tests
 * @version    $Revision: 637 $
 * @backupStaticAttributes disabled
 */
class OauthTest extends RestTest
{
	protected function setUp()
	{
		parent::setUp();

		if(!$this->hasService('org.amun-project.oauth'))
		{
			$this->markTestSkipped('Service oauth not installed');
		}
	}

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet('tests/amun.xml');
	}

	public function getEndpoint()
	{
		return $this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . 'api/oauth';
	}

	public function getTable()
	{
		return DataFactory::getTable('Oauth');
	}

	public function testGet()
	{
		$this->assertResultSetResponse($this->get());
	}

	public function testPost()
	{
		$record = $this->getTable()->getRecord();
		$record->setStatus(Oauth\Record::NORMAL);
		$record->setName('foo');
		$record->setEmail('foo@bar.com');
		$record->setUrl('http://google.de');
		$record->setTitle('foobar');
		$record->setDescription('foobar');

		$this->assertPositiveResponse($this->post($record));

		$actual = $this->table->getRow(array('status', 'name', 'email', 'url', 'title', 'description'), new Condition(array('id', '=', 2)));
		$expect = array_map('strval', $record->getData());

		$this->assertEquals($expect, $actual);
	}
}

