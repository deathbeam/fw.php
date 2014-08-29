<?php
return Db::getInstance();

class Db extends Library {
    private $dbh;
	private $stmt;
	
	public function init($hobo) {
		$dsn = $hobo->get('DB_TYPE').':host='.$hobo->get('DB_HOST').';dbname='.$hobo->get('DB_NAME');
        $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        );
        $this->dbh = new PDO($dsn, $hobo->get('DB_USER'), $hobo->get('DB_PASS'), $options);
	}
	
	public function query($query){
		$this->stmt = $this->dbh->prepare($query);
	}
	public function bind($param, $value, $type = null){
		if (is_null($type)) {
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
			}
		}
		$this->stmt->bindValue($param, $value, $type);
	}
	public function execute(){
		return $this->stmt->execute();
	}
	public function fetchRows(){
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function fetchRow(){
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}
	public function rowCount(){
		return $this->stmt->rowCount();
	}
	public function lastInsertId(){
		return $this->dbh->lastInsertId();
	}
	public function beginTransaction(){
		return $this->dbh->beginTransaction();
	}
	public function endTransaction(){
		return $this->dbh->commit();
	}
	public function cancelTransaction(){
		return $this->dbh->rollBack();
	}
}