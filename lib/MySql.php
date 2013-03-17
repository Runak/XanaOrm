<?php
class Mysql{
 	static $_instance;
	public $LastInsertId=0;

	public $DataBaseName = "webApp1_static";//default database name
	
	public $CharSet="utf8";
	

	public $DataBase   = null;
	public $LoadConnection=0;
	
	public $TRSS  = array();
	public $TRANS =false;
	
	public function getInstance() {
		return $this;
	}
	
	public function StartTransaction(){
		$this->TRANS =true;
	}
	
	public function Commit(){
		$this->TRANS =false;
		$this->Query("SET AUTOCOMMIT = 0");
		$this->Query("START TRANSACTION");

		$K=1;
		$TRSS = $this->TRSS;
		foreach ($TRSS as $T){
			$L = $this->Query($T);
			$K = $K and $L;
		}
		
		if($K){
			$this->Query("COMMIT");
		}else{
			$this->Query("ROLLBACK");
		}
		$this->TRSS = array();
		
	}
	public function MySqlQuery($S,$U,$P,$D,$Q) {
		if(!isset($Connection)){
			$this->Connection = @mysql_connect($S,$U,$P);
			if($this->Connection){
				mysql_query("Set NAMES {$this->CharSet}",$this->Connection);	
			}else{
				throw new Exception("Data Base Error", 1);
			}
		}
		$cnn = $this->Connection ;
		$db = mysql_select_db($D,$cnn);

		$res = mysql_query($Q,$cnn);
		$this->LastInsertId = mysql_insert_id($cnn);
		return $res;
	}
	
	public function Query($sql){
		if($this->TRANS==true){
			$this->TRSS[]=$sql;
		}else {
			$res = $this->MySqlQuery(
				Config::$DbServer	,
				Config::$DbUser		,
				Config::$DbPassword	,
				$this->DataBaseName	,
				$sql
			);
			return $res;
		}
	}
	
	public function Fetch($Res){
		return mysql_fetch_assoc($Res);
	}
	
	public function LastInsertId(){
		return $this->LastInsertId;
	}
	
	public function BigCalc($A,$B=1,$OP="+"){
		$rs  = $this->Query("Select @a:=$A $OP $B ");
		$val = mysql_fetch_row($rs);
		return $val[0];
	}

	 
	
}


?>