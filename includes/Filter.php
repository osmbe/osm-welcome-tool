<?php

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
	
	protected function makeAppliedLink ($filter, $name) {
		$this->appliedFilterControl .= '<span class="Filter-filter"><a href="?filter='.static::filterUrlEncode($filter).'">x</a> '
			. $name
			. '</span> ';
	}
	
	protected function makeUnappliedLink ($filter, $name) {
		$this->unappliedFilterControl .= '<span class="Filter-filter"><a href="?filter='.static::filterUrlEncode($filter).'">'
			. $name
			. '</a></span> ';
	}
	
	protected function filterUrlEncode ($filter) {
		return base_convert($filter, 10, 36);
	}

}

?>