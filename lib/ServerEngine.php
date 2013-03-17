<?php
class Server extends MySql{
   static $_instance;
 	 public $TRSS  = array();
 	 public $TRANS =false;
 	 public $Connection = null;
  		 
 	 public function getInstance() {
 	 	if(!(self::$_instance instanceof self))
      self::$_instance = new self();
 	 	return self::$_instance;
 	 }
 	 
	public function microtime_float()
	{
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}

	public function BackUp($path){

		$bpath =$path.'/'.$this->DataBaseName;
		$tables = $this->Query("SHOW FULL TABLES FROM `{$this->DataBaseName}`");
		$start =$this->microtime_float();
		
		Util::MakePath($bpath);
		Util::CleanDir($bpath);

		while ($row = mysql_fetch_row($tables)) {
			if($row[1]=='BASE TABLE'){
				$table = $row[0];
				$FILE = "$path/{$this->DataBaseName}/$table.samal";
				$sql  ="SET NAMES 'utf8'";
				mysql_query($sql);
				$sql ="SELECT * INTO OUTFILE '$FILE' FROM `$table`;";
				mysql_query($sql);
			}
		}
		$end =$this->microtime_float();
		return $start -$end;
}

 
class StaticServer extends Server{
 	  public $DataBaseName ="webApp1_static";
	     
}
 
class DynamicServer extends Server{
 	 public function __construct(){
    	$year=date('y');  
      $this->DataBaseName = "webApp1_data{$year}"; 
	 }
}


class FilesServer extends Server{
 	 public $DataBaseName ="webApp1_files";
}


?>