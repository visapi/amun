<?php
/*
 * amun
 * A social content managment system based on the psx framework. For
 * the current version and informations visit <http://amun.phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace AmunService\Core\Host\Request;

use Amun\Data\HandlerAbstract;
use Amun\Data\RecordAbstract;
use Amun\Exception;
use Amun\Filter as AmunFilter;
use Amun\Util;
use PSX\Data\WriterInterface;
use PSX\Data\WriterResult;
use PSX\DateTime;
use PSX\Filter;
use PSX\Util\Markdown;
use PSX\Url;

/**
 * Record
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://amun.phpsx.org
 */
class Record extends RecordAbstract
{
	protected $_host;
	protected $_account;
	protected $_date;

	public function setId($id)
	{
		$id = $this->_validate->apply($id, 'integer', array(new AmunFilter\Id($this->_table)), 'id', 'Id');

		if(!$this->_validate->hasError())
		{
			$this->id = $id;
		}
		else
		{
			throw new Exception($this->_validate->getLastError());
		}
	}

	public function setHostId($hostId)
	{
		$hostId = $this->_validate->apply($hostId, 'integer', array(new AmunFilter\Id($this->_hm->getTable('AmunService\Core\Host'))), 'hostId', 'Host Id');

		if(!$this->_validate->hasError())
		{
			$this->hostId = $hostId;
		}
		else
		{
			throw new Exception($this->_validate->getLastError());
		}
	}

	public function setUserId($userId)
	{
		$userId = $this->_validate->apply($userId, 'integer', array(new AmunFilter\Id($this->_hm->getTable('AmunService\User\Account'))), 'userId', 'User Id');

		if(!$this->_validate->hasError())
		{
			$this->userId = $userId;
		}
		else
		{
			throw new Exception($this->_validate->getLastError());
		}
	}

	public function setIp($ip)
	{
		$ip = $this->_validate->apply($ip, 'string', array(new Filter\Ip()), 'ip', 'Ip');

		if(!$this->_validate->hasError())
		{
			$this->ip = $ip;
		}
		else
		{
			throw new Exception($this->_validate->getLastError());
		}
	}

	public function setExpire($expire)
	{
		$expire = $this->_validate->apply($expire, 'string', array(new AmunFilter\DateInterval()), 'expire', 'Expire');

		if(!$this->_validate->hasError())
		{
			$this->expire = $expire;
		}
		else
		{
			throw new Exception($this->_validate->getLastError());
		}
	}

	public function getId()
	{
		return $this->_base->getUrn('core', 'host', 'request', $this->id);
	}

	public function getHost()
	{
		if($this->_host === null)
		{
			$this->_host = $this->_hm->getHandler('AmunService\Core\Host')->getRecord($this->hostId);
		}

		return $this->_host;
	}

	public function getUser()
	{
		if($this->_account === null)
		{
			$this->_account = $this->_hm->getHandler('AmunService\User\Account')->getRecord($this->userId);
		}

		return $this->_account;
	}

	public function getDate()
	{
		if($this->_date === null)
		{
			$this->_date = new DateTime($this->date, $this->_registry['core.default_timezone']);
		}

		return $this->_date;
	}
}


