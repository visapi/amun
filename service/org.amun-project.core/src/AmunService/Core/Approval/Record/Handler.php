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

namespace AmunService\Core\Approval\Record;

use Amun\Data\RecordAbstract;
use Amun\Data\HandlerAbstract;
use Amun\Exception;
use PSX\Data\RecordInterface;
use PSX\Sql\Condition;
use PSX\Sql\Join;

/**
 * Handler
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://amun.phpsx.org
 */
class Handler extends HandlerAbstract
{
	public function create(RecordInterface $record)
	{
		throw new Exception('You cant create a approval record');
	}

	public function update(RecordInterface $record)
	{
		if($record->hasFields('id'))
		{
			$sql = <<<SQL
SELECT
	`record`.`type`   AS `recordType`,
	`record`.`table`  AS `recordTable`,
	`record`.`record` AS `recordRecord`
FROM 
	{$this->table->getName()} `record`
WHERE 
	`record`.`id` = ?
SQL;

			$row  = $this->sql->getRow($sql, array($record->id));
			$data = unserialize($row['recordRecord']);

			if(!empty($data) && is_array($data))
			{
				$class   = $this->registry->getClassNameFromTable($row['recordTable']);
				$handler = $this->hm->getHandler($class);

				if($handler instanceof HandlerAbstract)
				{
					$handler->setIgnoreApprovement(true);

					$object = $handler->getTable()->getRecord();

					foreach($data as $k => $v)
					{
						$object->$k = $v;
					}

					switch($row['recordType'])
					{
						case 'INSERT':
							$handler->create($object);
							break;

						case 'UPDATE':
							$handler->update($object);
							break;

						case 'DELETE':
							$handler->delete($object);
							break;

						default:
							throw new Exception('Invalid record type');
					}
				}
				else
				{
					throw new Exception('Invalid record table');
				}
			}

			// delete the record
			$this->delete($record);

			return $record;
		}
		else
		{
			throw new Exception('Missing field in record');
		}
	}

	public function delete(RecordInterface $record)
	{
		if($record->hasFields('id'))
		{
			$con = new Condition(array('id', '=', $record->id));

			$this->table->delete($con);


			$this->notify(RecordAbstract::DELETE, $record);


			return $record;
		}
		else
		{
			throw new Exception('Missing field in record');
		}
	}

	protected function getDefaultSelection()
	{
		return $this->table
			->select(array('id', 'userId', 'type', 'table', 'record', 'date'))
			->join(Join::INNER, $this->hm->getTable('AmunService\User\Account')
				->select(array('name', 'profileUrl'), 'author')
			);
	}
}

