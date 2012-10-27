<?php
/*
 *  $Id: Approval.php 683 2012-06-03 11:52:32Z k42b3.x@googlemail.com $
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
 * Amun_System_Approval
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://amun.phpsx.org
 * @category   Amun
 * @package    Amun_System_Approval
 * @version    $Revision: 683 $
 */
class Amun_System_Approval extends Amun_Data_RecordAbstract
{
	public function setId($id)
	{
		$id = $this->_validate->apply($id, 'integer', array(new Amun_Filter_Id($this->_table)), 'id', 'Id');

		if(!$this->_validate->hasError())
		{
			$this->id = $id;
		}
		else
		{
			throw new PSX_Data_Exception($this->_validate->getLastError());
		}
	}

	public function setTable($table)
	{
		$table = $this->_validate->apply($table, 'string', array(new Amun_Filter_Table($this->_sql)), 'table', 'Table');

		if(!$this->_validate->hasError())
		{
			$this->table = $table;
		}
		else
		{
			throw new PSX_Data_Exception($this->_validate->getLastError());
		}
	}

	public function setField($field)
	{
		$field = $this->_validate->apply($field, 'string', array(new Amun_System_Approval_Filter_Field($this->_sql, $this->table)), 'field', 'Field');

		if(!$this->_validate->hasError())
		{
			$this->field = $field;
		}
		else
		{
			throw new PSX_Data_Exception($this->_validate->getLastError());
		}
	}

	public function setValue($value)
	{
		$this->value = $value;
	}

	public function getId()
	{
		return $this->_base->getUrn('system', 'approval', $this->id);
	}
}


