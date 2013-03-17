<?php
class Model {

	public $datamembers = array();
	protected $PrimaryKey="Id";
	protected $TableName ="Table";
	protected $AllFields = "*";
	protected $Server = null;
	
	public function setServer($Server){
    	$this->Server=$Server;
	}
	
	public function getInstance() {
		return $this->Server->getInstance();
	}
    public function StartTransaction(){
		return $this->Server->StartTransaction();
	}
	
	public function Commit(){
		return $this->Server->Commit();
	}
	
	public function Query($sql){
		return $this->Server->Query($sql);
	}
	
	public function Fetch($Res){
		return $this->Server->Fetch($Res);
	}
	
	public function LastInsertId(){
		return $this->Server->LastInsertId();
	}
	
	public function BigCalc($A,$B=1,$OP="+"){
		return $this->Server->BigCalc($A,$B,$OP);
	}

	public function __set($variable, $value){
		$this->datamembers[strtolower($variable)] = $value;
	}
	
	public function __get($variable){
		return $this->datamembers[strtolower($variable)];
	}
 
	public function HasField($name){
		return isset($this->datamembers[strtolower($name)]);
	}
	
	public function HasRecord($cond){
		return $this->Count($cond) > 0;
	}
	
	public function ToJson(){
    return json_encode($this->datamembers);
	}
	
	public function SimpleJoin($table,$cond="",$fields="*",$order=""){
		$tmp = $this->TableName ;
		$this->TableName ="`$tmp`, `$table`";

		$result = $this->Select($cond,$fields,$order);
		$this->TableName=$tmp ;
		return $result; 
	}
	
	public function LastId()
	{
		return $this->Max($this->PrimaryKey);
	}
	
	public function QueryModel($sql){
		$result= array();
		$r=$this->Query($sql);
		
		if(!$r)
			return $result;

		while($row = $this->Fetch($r)){
			$t= clone $this;
			foreach ($row as $k => $v){
				$k=strtolower($k);
				$t->$k=$v;
			}
			$result[]=$t;
		}
		return $result;
	}
	
	public function Select($cond="",$fields="",$order="",$limit="",$EXT=""){
    
		if($cond  !="") $cond  = " Where $cond "		;
		if($order !="") $order = " Order By $order "	;
		if($limit !="") $limit = " LIMIT $limit "	;
		if($fields=="") $fields = $this->AllFields	;
		
		$table = $this->TableName;
		$sql="SELECT $fields From $table $cond  $EXT $order $limit";
   	
		$r=$this->QueryModel($sql);
		return $r;
	}
	
	public function Count($cond=""){
		if($cond !="")$cond =" Where ".$cond;
		$table=$this->TableName;
		$r=$this->Query("select count(*) as cntx from $table $cond");
		$row = $this->Fetch($r);
		return $row['cntx'];
	}
	
	public function Sum($field,$cond=""){
		if($cond !="")$cond =" Where ".$cond;
		$table=$this->TableName;
		$sql = "select sum($field) as sumx from $table $cond";
		
		$r=$this->Query($sql);
		$row = $this->Fetch($r);
		
		return Val($row['sumx']);
	}
	
	public function Max($field,$cond=""){
		if($cond !="")$cond =" Where ".$cond;
		$table=$this->TableName;
		if($r=$this->Query("select Max(`$field`) as mx from `$table` $cond")){
			$row = $this->Fetch($r);
			return $row['mx'];		
		}else {
			return 0;
		}
	}
	
	public function All($order=""){
		if($order!="")
			$order=" order by $order ";
		return $this->QueryModel("SELECT {$this->AllFields} FROM `{$this->TableName}` $order");
	}
	public function Limit($limit=10,$cond="",$order=""){
		return $this->Select($cond,"{$this->AllFilds}",$order,$limit);
	}
	public function Filter($cond,$order=""){
		return $this->Select($cond,"{$this->AllFields}" , $order);
	}
	public function FindByCond($cond,$order=""){
			if($order != "") $order =" order by $order";
			$sql= "Select * from {$this->TableName} Where $cond $order" ;
			$ret= $this->QueryModel($sql);
			if(count($ret)>0)
        return $ret[0];
	}
	public function Find($field,$value,$op = "="){
		$fd=($this->Select($field .$op . "'".addslashes($value)."'"));
		if(count($fd)>0)
			return $fd[0];
	}

	public function FindAll($cond=""){
		return $this->Select($cond);
	}
	
	public function FindByKey($key){
   // echo "/* {$this->PrimaryKey} */";
		$fd=$this->Select($this->PrimaryKey ."='".mysql_escape_string($key)."'");
		if(count($fd)>0)
			return $fd[0];
	}
	public function UpdateByCond($cond,$field="*"){
		$ff=array();
		$table=$this->TableName;
		if($field=="*"){
			foreach ($this->datamembers as $k => $v)
				if($k != $this->PrimaryKey)
					$ff[]= "`".$k."`='" . mysql_real_escape_string($v)."'";
			$ff=implode(",", $ff);
		}else{
			$ff="`$field`='".$this->$field."'";
		}
	
		$sql="UPDATE `$table` SET $ff where $cond";
		$this->Query($sql);

	}
	public function SpecialUpdate($value,$cond){
		$sql="UPDATE `{$this->TableName}` SET $value where $cond";
		$this->Query($sql);
    
	}

	public function ForceUpdate()
	{
		$hasPK = $this->HasField($this->PrimaryKey);
		
		if($hasPK)	$this->Update();
		else 		$this->Save();

	}
	
	public function Update($field="*"){
		$cond = $this->PrimaryKey;
		
		if(!empty($cond))
			$cond = " `$cond` ='".mysql_real_escape_string($this->$cond)."'";
		else
			$cond="";		
		
		//print_r($this);
		
		$this->UpdateByCond($cond,$field);
	}
	
	public function Clear(){
		$table=$this->TableName;
		$this->Query("TRUNCATE TABLE `$Table`");
	}
	
	public function Delete($key=""){
		$table=$this->TableName;
		$k=$this->PrimaryKey;
		if(!empty($key))
      $v=$key;
    else
      $v=$this->$k;
		$sql="DELETE FROM $table WHERE `$k`='$v'";
		$this->Query($sql);
		//echo $sql;
	}
	
	public function DeleteByCond($cond){
		$table=$this->TableName;
		$sql="DELETE FROM $table WHERE $cond ";
		$this->Query($sql);
	}
	
	
	public function getLastKey(){
		  $mx= $this->Max($this->PrimaryKey);
		  return $mx;
	}
	
	
	public function Save(){
		$fs=array();
		$vs=array();
		$table=$this->TableName;
		foreach ($this->datamembers as $k => $v)
		{
			$fs[]= "`".$k."`";
			$vs[]="'" . addslashes($v)."'";
		}
		$fs=implode(",", $fs);
		$vs=implode(",", $vs);
		$sql="INSERT INTO $table ($fs) VALUES ($vs) ";
		$this->Query($sql);
		if($this->datamembers[strtolower($this->PrimaryKey)]!="*")
			$this->datamembers[strtolower($this->PrimaryKey)] = $this->LastInsertId();
	}	 


	public function LockTable($mod="write")
	{
		$this->Query("LOCK TABLES `{$this->TableName}` $mod");
	}

	public function UnlockTable()
	{
		$this->Query("UNLOCK TABLES");
	}

}

class StaticModel extends Model {
	public function __construct(){
		$this->Server = StaticServer::getInstance();
	}
}


class DynamicModel extends Model {
	public function __construct(){
		$this->Server = DynamicServer::getInstance();
	}
}


class FilesModel extends Model {
	public function __construct(){
		$this->Server = FilesServer::getInstance();
	}
}


?>