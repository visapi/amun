<?php
/*
 *  $Id: PluginTest.php 743 2012-06-26 19:31:26Z k42b3.x@googlemail.com $
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

/**
 * Amun_Api_Service_PluginTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://amun.phpsx.org
 * @category   tests
 * @version    $Revision: 743 $
 * @backupStaticAttributes disabled
 */
class Amun_Api_PluginTest extends Amun_Api_RestTest
{
	protected function setUp()
	{
		if(!$this->hasService('org.amun-project.plugin'))
		{
			$this->markTestSkipped('Service plugin not installed');
		}
		else
		{
			parent::setUp();
		}
	}

	public function getEndpoint()
	{
		return $this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . 'api/plugin';
	}

	public function getTable()
	{
		return Amun_Sql_Table_Registry::get('Plugin');
	}

	public function testGet()
	{
		$this->assertResultSetResponse($this->get());
	}
}

