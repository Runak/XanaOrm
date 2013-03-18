<?php
class User extends StaticModel {
   protected $TableName = 'users';
   
    
   public function getProfile($user=null){
   	$p = new Profile();
    if($user == null)
      return $p->FindByKey($this->Id);  
   	return $p->FindByKey($user->Id);
   } 

   public function getFullName()
   {
     return $this->getProfile()->getFullName();
   }
 
 
   public function getHash(){
    return sha1($user->UserName .'PrivatePassword'.$user->Password);
   }
   
   public function ChangePassword(){
	   $this->Password = $this->getHash();
	   $this->Update("Password");
   }
   
   public function Login(){
	   $user = $this->UserName;
	   $pass = $this->getHash();
	   $find = $this->FindByCond("`UserName`='$user' and `Password`='$pass'");
	   if(isset($find)){
      return $find;
	   }else{
      return "LOGIN::FAILD";
	   }
   }
  
}
 

?>
