<?php

  include "lib/Config.php";
  include "lib/MySql.php";
  include "lib/ServerEngine.php";
  include "lib/Model.php";
  
  include "lib/Models/user.php";
  include "lib/Models/profile.php";
  
  
  
  //Finding an Object
  
  $u =new User();
  $u = $u->FIndByKey(600032);
  echo "UserName: {$u->UserName}\n";
  echo "Access Level: {$u->AccessLevel}\n";
  echo "Createion Date: {$u->CreationDate}\n\n";
  echo "Profile :\n";
  echo "---------\n";
  $p = $u->getProfile();
  echo "Full Name: {$p->FirstName} {$p->LastName}\n";
  echo '<img src="'.$p->getAvatar().'" width="256px" />';
  
  // get JSON Output
  echo $p->getJSON();
  
  
  //Edit an information of field in an object
  $p->BirthDate = "2706/12/01"; 
  $p->Sex       = 0;
  $p->Update();
   
  //Find and Delete an Item
  $u2 = new User();
  $u2 = $u2->FindByKey(600015);
  $u2->Delete();
  
  
  //insert a new user into database
  $newUser = new User();
  $user->UserName ="Support";
  $user->AccessLevel=0c23f;//0000 1100 0010 0011 1111
  $user->Save();//
  
  //Execute special functions
  $user->ChangePassword("this is new password");
  
  //and blah blah blah
  

?>