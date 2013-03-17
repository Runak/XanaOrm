<?php
class Profile extends StaticModel {
   protected $TableName  = 'profile';
   protected $PrimaryKey = 'userid';
   

   public function getFullName($uid=""){
   	if($uid =="")
   		$x= $this;
   	else
   		$x=$this->FindByKey($uid);
   	return $x->FirstName ." ".$x->LastName;
   }
   
   
   public function getAvatar(){
    $av = new Avatar();
    return $av->getAvatar($this->UserId);
   }
   
   public function setAvatar($Avatar){
    $av = new Avatar();
    return $av->setAvatar($this->UserId,$Avatar);
   }
   
}

class Avatar extends FileModel{
   protected $PrimaryKey = 'userid';
   protected $TableName  = 'user_attachments';
   
   public function getAvatar($userId){
    $tmp = $this->FindByKey($userId);
    return $tmp->Avatar;
   }
   
   public function setAvatar($userId,$base64Img){
    $tmp = $this->FindByKey($userId);
    $tmp->Avatar = $base64Img;
    $tmp->Update("`Avatar`");
   }
  
}
?>
