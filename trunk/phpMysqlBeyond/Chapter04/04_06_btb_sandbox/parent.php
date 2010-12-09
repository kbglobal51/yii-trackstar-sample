<?php

class A {
	static $a=1;
	
	static function modified_a() {
		return self::$a + 10;
	}
}


?>