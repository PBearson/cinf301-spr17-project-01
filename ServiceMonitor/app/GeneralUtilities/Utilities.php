<?php
class Utilities
{	
	public function __construct()
	{
		$args = $_SERVER['argv'];
		$this->argv = $this->parse($args);
	}
	
	/**
	 * Parse command-line arguments
	 * @param array $a the command-line arguments
	 * @return array of parsed arguments
	 */
	private function parse(array $a)
	{
		$args = array();
		
		for($i = 0; $i < count($a); $i++)
		{
			$val = $a[$i];
			
			//A single-dash followed by a value/list of values
			if($val[0] == "-" and $val[1] != "-")
			{
				//A single-dash that is self-contained
				//Example: '-v'
				if($i + 1 >= sizeof($a) or $a[$i + 1][0] == "-")
				{
					$args[$val[1]] = 'true';
				}
				
				//A single dash followed by a string
				//Example: -T 4
				else if(!strpos($a[$i + 1], ','))
				{
					$args[$val[1]] = $a[$i + 1];
				}
				
				//A single dash followed by an array list of values
				//Example: -l Bryan,Kim,Austin
				else 
				{
					$subargs = $a[$i + 1];
					$subargsarr = array();
					$currarg = "";
					for($j = 0; $j <= strlen($subargs); $j++)
					{
						$char = substr($subargs, $j, 1);
						if($char == "," or $j == strlen($subargs))
						{
							array_push($subargsarr, $currarg);
							$currarg = "";
						}
						else
						{
							$currarg .= $char;
						}
					}
					$args[$val[1]] = $subargsarr;
				}
			}
		}
		return $args;
	}
}
