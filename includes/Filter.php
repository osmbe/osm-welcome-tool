<?php
/* This file is part of osm-welcome: a platform to coordinate welcoming of OpenStreetMap mappers
 * Copyright © 2018  Midgard and osm-welcome contributors
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU Affero General Public License as published by the Free Software Foundation, either version 3
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with this
 * program. If not, see <https://www.gnu.org/licenses/>.
 */

class Filter {

	const MODE_EXCLUDING = 1;
	const MODE_COMBINING = 2;

	private $paramName = 'filter';
	
	private $filters = array();
	private $applied = 0;
	
	private $appliedFilterControl = '';
	private $unappliedFilterControl = '';
	
	public function __construct ($filters) {
		$this->filters = $filters;
		$this->applied = intval(@$_GET['filter'], 36);
		
		foreach ($this->filters as $filterGroup) {
			if (isset($filterGroup['default'])) {
				$oneOfGroupApplied = false;
				foreach ($filterGroup as $filter=>$name) {
					if ($filter&$this->applied) {
						$oneOfGroupApplied = true;
						break;
					}
				}
				if (!$oneOfGroupApplied) {
					$this->applied |= $filterGroup['default'];
				}
			}
		}
	}
	
	public function isEnabled ($filter) {
		return ($filter&$this->applied)?true:false;
	}
	
	public function printControls () {
		static::processFilter();
		
		echo '<div class="Filter-unappliedfilters">';
		echo $this->unappliedFilterControl;
		echo '</div>';
		
		echo '<div class="Filter-appliedfilters">';
		if ($this->appliedFilterControl) {
			echo 'Applied filters: ';
			echo $this->appliedFilterControl;
			echo '<a href="?" class="Filter-clearall">Clear all</a>';
		}
		echo '</div>';
	}
	
	protected function processFilter () {
		foreach ($this->filters as $filterGroup) {
		
			$this->unappliedFilterControl .= '<span class="Filter-filtergroup">';
			
			$group = 0;
			if (@$filterGroup['mode'] !== static::MODE_COMBINING) {
				foreach ($filterGroup as $filter=>$name) {
					$group |= $filter;
				}
			}
			if (isset($filterGroup['name'])) {
				$this->unappliedFilterControl .= '<span class="Filter-title"> '
					. $filterGroup['name']
					. '</span>';
			}
			
			foreach ($filterGroup as $filter=>$name) {
			
				if (!is_int($filter)) {
					continue;
				}
			
				if ($filter & $this->applied) {
					static::makeAppliedLink(
						$this->applied & ~$filter,
						is_array($name) ? $name[1] : $name
					);
				} else {
					static::makeUnappliedLink(
						$filter | ($this->applied & ~$group),
						is_array($name) ? $name[0] : $name
					);
				}
				
			}
			
			$this->unappliedFilterControl .= '</span>';
		}
	}
	
	protected function makeAppliedLink ($filter_number, $name) {
		$this->appliedFilterControl .= '<span class="Filter-filter"><a href="'.static::getUrlForNumber($filter_number).'">x</a> '
			. $name
			. '</span> ';
	}
	
	protected function makeUnappliedLink ($filter_number, $name) {
		$this->unappliedFilterControl .= '<span class="Filter-filter"><a href="'.static::getUrlForNumber($filter_number).'">'
			. $name
			. '</a></span> ';
	}
	
	protected function getUrlForNumber ($filter_number) {
		return '?filter='.base_convert($filter_number, 10, 36);
	}
	
	public function getUrl () {
		return static::getUrlForNumber($this->applied);
	}

}

?>
