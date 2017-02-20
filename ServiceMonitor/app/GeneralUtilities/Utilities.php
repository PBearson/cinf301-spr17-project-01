<?php
class Utilities
{	
	public function __get($key)
	{
		if($key == 'argv')
		{
			return $this->parse($_SERVER['argv']);
		}
		else return NULL;
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
			
			//A double dash followed by a value/list of values
			else if ($val[0] == "-" and $val[1] == "-")
			{
				//Get the value to the left of the = sign
				$key = substr($val, 2, strpos($val, "=") - 2);
				
				//Get the value to the right of the sign
				$arg = substr($val, strpos($val, "=") + 1, strlen($val));
				
				//Argument is a string
				//Example: --type=gold
				if(!strpos($arg, ","))
				{
					$args[$key] = $arg;
				}
				
				//Argument is an array
				//Example --names=Austin,Duncan,Eddie
				else
				{
					$argsarr = array();
					$currarg = "";
					for($j = 0; $j <= strlen($arg); $j++)
					{
						$char = substr($arg, $j, 1);
						if($char == "," or $j == strlen($arg))
						{
							array_push($argsarr, $currarg);
							$currarg = "";
						}
						else
						{
							$currarg .= $char;
						}
					}
					$args[$key] = $argsarr;
				}
			}
		}
		return $args;
	}
}
