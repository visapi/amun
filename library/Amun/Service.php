<?php
/*
 *  $Id: Page.php 840 2012-09-11 22:19:37Z k42b3.x@googlemail.com $
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
 * Amun_Service
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://amun.phpsx.org
 * @category   Amun
 * @package    Amun_Service
 * @version    $Revision: 840 $
 */
class Amun_Service
{
	private $config;
	private $sql;
	private $registry;
	private $user;

	private $_record;
	private $_handler;
	private $_table;
	private $_form;

	public $id;
	public $status;
	public $source;
	public $name;
	public $path;
	public $namespace;
	public $type;
	public $link;
	public $author;
	public $license;
	public $version;
	public $date;

	public function __construct($id, Amun_Registry $registry, Amun_User $user)
	{
		$this->config   = $registry->getConfig();
		$this->sql      = $registry->getSql();
		$this->registry = $registry;
		$this->user     = $user;

		if(is_numeric($id))
		{
			$column = 'id';
		}
		else if($id[0] == '/')
		{
			$column = 'path';
		}
		else
		{
			$column = 'source';
		}

		$status = AmunService_Core_Content_Service_Record::NORMAL;
		$sql    = <<<SQL
SELECT

	service.id        AS `serviceId`,
	service.status    AS `serviceStatus`,
	service.source    AS `serviceSource`,
	service.name      AS `serviceName`,
	service.path      AS `servicePath`,
	service.namespace AS `serviceNamespace`,
	service.type      AS `serviceType`,
	service.version   AS `serviceVersion`,
	service.date      AS `serviceDate`

	FROM {$this->registry['table.core_content_service']} `service`

		WHERE `service`.`{$column}` = ?
SQL;

		$row = $this->sql->getRow($sql, array($id));

		if(!empty($row))
		{
			$this->id        = $row['serviceId'];
			$this->status    = $row['serviceStatus'];
			$this->source    = $row['serviceSource'];
			$this->name      = $row['serviceName'];
			$this->path      = $row['servicePath'];
			$this->namespace = $row['serviceNamespace'];
			$this->type      = $row['serviceType'];
			$this->version   = $row['serviceVersion'];
			$this->date      = $row['serviceDate'];
		}
		else
		{
			throw new Amun_Exception('Invalid service');
		}
	}

	public function getRecord($ns = null)
	{
		if($this->_record === null)
		{
			$name  = $ns !== null ? $ns . '_Record' : 'Record';
			$class = self::getClass($this->namespace, $name);

			if($class !== null && $class->isSubclassOf('Amun_Data_RecordAbstract'))
			{
				$this->_record = $class->newInstance($this->getTable());
			}
		}

		return $this->_record;
	}

	public function getHandler($ns = null)
	{
		if($this->_handler === null)
		{
			$name  = $ns !== null ? $ns . '_Handler' : 'Handler';
			$class = self::getClass($this->namespace, $name);

			if($class !== null && $class->isSubclassOf('Amun_Data_HandlerAbstract'))
			{
				$this->_handler = $class->newInstance($this->user);
			}
		}

		return $this->_handler;
	}

	public function getTable($ns = null)
	{
		if($this->_table === null)
		{
			$name  = $ns !== null ? $ns . '_Table' : 'Table';
			$class = self::getClass($this->namespace, $name);

			if($class !== null && $class->isSubclassOf('Amun_Sql_TableAbstract'))
			{
				$this->_table = $class->newInstance($this->registry);
			}
		}

		return $this->_table;
	}

	public function getForm($ns = null)
	{
		if($this->_form === null)
		{
			$name  = $ns !== null ? $ns . '_Form' : 'Form';
			$class = self::getClass($this->namespace, $name);

			if($class !== null && $class->isSubclassOf('Amun_Data_FormAbstract'))
			{
				$this->_form = $class->newInstance($this->getApiEndpoint());
			}
		}

		return $this->_form;
	}

	public function hasRight($rightName)
	{
		return $this->user->hasRight($this->getRightName($rightName));
	}

	public function hasViewRight()
	{
		return $this->hasRight('view');
	}

	public function hasAddRight()
	{
		return $this->hasRight('add');
	}

	public function hasEditRight()
	{
		return $this->hasRight('edit');
	}

	public function hasDeleteRight()
	{
		return $this->hasRight('delete');
	}

	public function getApiEndpoint()
	{
		return $this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . 'api' . $this->path;
	}

	protected function getRightName($actionName)
	{
		return strtolower($this->namespace . '_' . $actionName);
	}

	public static function getClass($namespace, $className)
	{
		try
		{
			$class = Amun_Registry::getClassName('AmunService_' . $namespace . '_' . $className);

			return new ReflectionClass($class);
		}
		catch(ReflectionException $e)
		{
			return null;
		}
	}
}