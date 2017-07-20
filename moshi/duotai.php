<?php

//多态
abstract class Db{
	public abstract function conn();
}

class MysqlDb extends Db{
	public function conn(){
		echo 'mysql conn<br/>';
	}
}
class OrcaleDb extends Db {
	public function conn(){
		echo 'orcale conn<br/>';
	}
}

class CreateDb {
	public static function create(Db $db){
		$db -> conn();
	}
}

CreateDb :: create(new MysqlDb());
CreateDb :: create(new OrcaleDb());
