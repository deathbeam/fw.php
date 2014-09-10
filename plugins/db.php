<?php
return Db::getInstance();

class Db extends Plugin {
    private $dbh;
	private $stmt;
	
	public function init($fw) {
		if (!$fw->exists('db_config')) return;
		$config = $fw->get('db_config');
		$dsn = $config[0].':host='.$config[1].';dbname='.$config[2];
		$options = array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
		$this->dbh = new PDO($dsn, $config[3], $config[4], $options);
	}
	
	public function query($query){
		$this->stmt = $this->dbh->prepare($query);
		return $this;
	}
	
	public function bind($param, $value, $type = null) {
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
		return $this;
	}
	
	public function execute() {
		return $this->stmt->execute();
	}
	
	public function fetchRows() {
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function fetchRow() {
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	public function rowCount() {
		return $this->stmt->rowCount();
	}
	
	public function lastInsertId() {
		return $this->dbh->lastInsertId();
	}
	
	public function beginTransaction() {
		return $this->dbh->beginTransaction();
	}
	
	public function endTransaction() {
		return $this->dbh->commit();
	}
	
	public function cancelTransaction() {
		return $this->dbh->rollBack();
	}
}