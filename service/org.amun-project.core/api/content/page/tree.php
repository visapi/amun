<?php
/*
 *  $Id: tree.php 856 2012-09-28 20:27:35Z k42b3.x@googlemail.com $
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
 * tree
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://amun.phpsx.org
 * @category   module
 * @package    api
 * @subpackage content_page
 * @version    $Revision: 856 $
 */
class tree extends Amun_Module_ApiAbstract
{
	public function onLoad()
	{
		if($this->getProvider()->hasViewRight())
		{
			try
			{
				$this->setResponse(new PSX_Data_Record('tree', array($this->buildTreeArray())));
			}
			catch(Exception $e)
			{
				$msg = new PSX_Data_Message($e->getMessage(), false);

				$this->setResponse($msg);
			}
		}
		else
		{
			$msg = new PSX_Data_Message('Access not allowed', false);

			$this->setResponse($msg, null, $this->user->isAnonymous() ? 401 : 403);
		}
	}

	private function buildTreeArray()
	{
		$sql = <<<SQL
SELECT

	`page`.`id`,
	`page`.`parentId`,
	`page`.`status`,
	`page`.`sort`,
	`page`.`path`,
	`page`.`title`,
	`page`.`urlTitle`,
	(CHAR_LENGTH(path) - CHAR_LENGTH(REPLACE(path, "/", ""))) AS `depth`

	FROM {$this->registry['table.core_content_page']} `page`

		ORDER BY CHAR_LENGTH(`page`.`path`), `page`.`sort` ASC
SQL;

		$result = $this->sql->getAll($sql);
		$tree   = array();

		foreach($result as $row)
		{
			$this->buildPath($tree, $row['path'], $row);
		}

		return $tree;
	}

	private function buildPath(array &$tree, $path, array $row)
	{
		if(empty($path))
		{
			$tree = array(

				'id'       => $row['id'],
				'status'   => $row['status'],
				'sort'     => $row['sort'],
				'path'     => $row['path'],
				'text'     => $row['urlTitle'],
				'children' => array(),

			);
		}
		else
		{
			$path = ltrim($path, '/');
			$pos  = strpos($path, '/');

			if($pos !== false)
			{
				$name = substr($path, 0, $pos);
				$rest = substr($path, $pos);
			}
			else
			{
				$name = $path;
				$rest = null;
			}

			$found = false;
			$sort  = array();

			foreach($tree['children'] as $k => $node)
			{
				$sort[] = $node['sort'];

				if($node['text'] == $name)
				{
					if(empty($rest))
					{
						$node['children'][] = array(

							'id'       => $row['id'],
							'status'   => $row['status'],
							'sort'     => $row['sort'],
							'path'     => $row['path'],
							'text'     => $row['urlTitle'],
							'children' => array(),

						);

						$found = true;

						break;
					}
					else
					{
						$this->buildPath($tree['children'][$k], $rest, $row);
					}
				}
			}

			array_multisort($sort, SORT_ASC, $tree['children']);

			if(empty($rest) && !$found)
			{
				$tree['children'][] = array(

					'id'       => $row['id'],
					'status'   => $row['status'],
					'sort'     => $row['sort'],
					'path'     => $row['path'],
					'text'     => $row['urlTitle'],
					'children' => array(),

				);
			}


		}
	}

	/*
	private function buildTreeArray($id = 0)
	{
		$node = array();
		$sql  = <<<SQL
SELECT

	page.id      AS `pageId`,
	page.status  AS `pageStatus`,
	page.title   AS `pageTitle`

	FROM {$this->registry['table.core_content_page']} page

		WHERE `page`.`parentId` = ?

		ORDER BY `page`.`sort` ASC
SQL;

		$result = $this->sql->getAll($sql, array($id));

		foreach($result as $row)
		{
			array_push($node, array(

				'id'       => $row['pageId'],
				'status'   => $row['pageStatus'],
				'text'     => $row['pageTitle'],
				'children' => $this->buildTreeArray($row['pageId']),

			));
		}

		return $node;
	}
	*/
}

