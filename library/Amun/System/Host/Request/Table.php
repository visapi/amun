<?php
/*
 *  $Id: Table.php 811 2012-07-09 14:23:49Z k42b3.x@googlemail.com $
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
 * Amun_System_Host_Request_Table
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://amun.phpsx.org
 * @category   Amun
 * @package    Amun_System_Host
 * @version    $Revision: 811 $
 */
class Amun_System_Host_Request_Table extends Amun_Sql_TableAbstract
{
	public function getConnections()
	{
		return array(

			'hostId' => $this->registry['table.system_host'],
			'userId' => $this->registry['table.user_account'],

		);
	}

	public function getName()
	{
		return $this->registry['table.system_host_request'];
	}

	public function getColumns()
	{
		return array(

			'id' => self::TYPE_INT | 10 | self::PRIMARY_KEY,
			'hostId' => self::TYPE_INT | 10,
			'userId' => self::TYPE_INT | 10,
			'ip' => self::TYPE_VARCHAR | 40,
			'token' => self::TYPE_VARCHAR | 40,
			'tokenSecret' => self::TYPE_VARCHAR | 40,
			'expire' => self::TYPE_VARCHAR | 25,
			'date' => self::TYPE_DATETIME,

		);
	}
}

