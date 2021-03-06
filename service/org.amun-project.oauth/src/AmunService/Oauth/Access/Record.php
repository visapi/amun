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

namespace AmunService\Oauth\Access;

use Amun\Data\HandlerAbstract;
use Amun\Data\RecordAbstract;
use Amun\Exception;
use Amun\Filter as AmunFilter;
use Amun\Util;
use PSX\Data\WriterInterface;
use PSX\Data\WriterResult;
use PSX\DateTime;
use PSX\Filter;
use PSX\Sql\Condition;
use PSX\Util\Markdown;

/**
 * Record
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://amun.phpsx.org
 */
class Record extends RecordAbstract
{
	protected $_api;
	protected $_account;
	protected $_date;
	protected $_rights;

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

	public function setApiId($apiId)
	{
		$apiId = $this->_validate->apply($apiId, 'integer', array(new AmunFilter\Id($this->_hm->getTable('AmunService\Oauth'))), 'apiId', 'Api Id');

		if(!$this->_validate->hasError())
		{
			$this->apiId = $apiId;
		}
		else
		{
			throw new Exception($this->_validate->getLastError());
		}
	}

	public function setAllowed($allowed)
	{
		$this->allowed = $allowed ? 1 : 0;
	}

	public function getId()
	{
		return $this->_base->getUrn('oauth', 'access', $this->id);
	}

	public function getApi()
	{
		if($this->_api === null)
		{
			$this->_api = $this->_hm->getHandler('AmunService\Oauth')->getRecord($this->apiId);
		}

		return $this->_api;
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

	public function getRights()
	{
		if($this->_rights === null)
		{
			$con = new Condition(array('accessId', '=', $this->id));

			$this->_rights = $this->_hm->getTable('AmunService\Oauth\Access\Right')->getCol('rightId', $con);
		}

		return $this->_rights;
	}
}


