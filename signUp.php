<?php 
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	include "UserClass.php";
session_start();
	if(isset($_POST['Fullname']) && isset($_POST['email']) && isset($_POST['handle']) && isset($_POST['password']) && isset($_POST['Cpassword']))
	{
		$new_User = new User();

		if ($_POST['password']== $_POST['Cpassword'])
		{
			$new_User->setUserEmail($_POST['email']);
			$new_User->setUserHandle($_POST['handle']);
			if($new_User->checkEmailHandle())
			{
				$Name = stripslashes(htmlspecialchars($_POST['Fullname']));
				$Email = stripslashes(htmlspecialchars($_POST['email']));
				$Handle = stripslashes(htmlspecialchars($_POST['handle']));
				$password = stripslashes(htmlspecialchars($_POST['Cpassword']));
				$new_User->setUserEmail($Email);
				$new_User->setUserFullname($Name);
				$new_User->setUserHandle($Handle);
				$new_User->setUserPassword($password);
				$new_User->SignUpUser();
				/*	echo $Name ." <br> ".$Email ." <br> " .$Handle ." <br> " .$password ;*/
			}elseif(isset($_GET['error'])){
			$error = $_GET['error'];
			header("Location:index2.php?error=$error");
			}

			


		}else{
			header("Location:index2.php?error=7");
		}


	}


	if(isset($_SESSION['access_token'])&& isset($_SESSION['avatarLink'])){

		$new_User = new User();
		$new_User->setUserEmail($_GET['useremail']);
		$new_User->setUserHandle($_GET['userhandle']);
		$new_User->setUserFullname($_GET['username']);

		if($_GET['username'] == ''){
			 
			 $userName =  str_replace('@', '', $_GET['userhandle']);
		}else{
			$userName = $_GET['username'];
		}


		echo  strlen($_SESSION['access_token']) ."String length over here <br>";
		
		if(isset($_GET['error'])){
			///updata access_token used as password in the data base
			try{
				include 'dbconnect.php';
				$Email = stripslashes(htmlspecialchars($_GET['useremail']));
				

				$queryUpdatePWD = $Connection->prepare("UPDATE Users SET userPassword=:temp WHERE userEmail=:userMtemp");
				$queryUpdatePWD->execute(array('temp' => $_SESSION['access_token'], 'userMtemp' => $Email));
					header("Location:Login.php?email=" .$_GET['useremail'] ."&password=" .$_SESSION['access_token']);
				echo "Already sign up just changing password<br> Confirming sign up<br>";
				/*$ConFirm = $Connection->prepare("SELECT userEmail,userPassword FROM Users");
				$ConFirm->execute();
				$result = $ConFirm->fetchAll();
				echo var_dump($result);*/
			}catch(PDOException $e){
			echo $e->getMessage();
			}
			
		}elseif($new_User->checkEmailHandle())
		{	
			$Name = stripslashes(htmlspecialchars($userName));
			$Email = stripslashes(htmlspecialchars($_GET['useremail']));
			$Handle = stripslashes(htmlspecialchars($_GET['userhandle']));
			$password = stripslashes(htmlspecialchars($_SESSION['access_token']));
			$new_User->setUserEmail($Email);
			$new_User->setUserFullname($Name);
			$new_User->setUserHandle($Handle);
			$new_User->setUserPassword($password);
			$new_User->SignUpUser();
			echo "In check email Handle";
			/*
			$ConFirm = $Connection->prepare("SELECT * FROM Users");
			$ConFirm->execute();
			$result = $ConFirm->setFetchMode(PDO::FETCH_ASSOC);
			echo var_dump($result);
			header("Location:Login.php?email=" .$_GET['useremail'] ."&password=" .$_SESSION['access_token']);*/
		}
		
	}



	if(isset($_POST['googleUserName']) && isset($_POST['imgLinkGoogle']) && isset($_POST['emailGoogle']) && isset($_POST['handleGoogle'])){
		$new_User = new User();
		$handle = '@' .$_POST['handleGoogle'];
		$new_User->setUserEmail($_POST['emailGoogle']);
		$new_User->setUserHandle($handle);
		$new_User->setUserFullname($_POST['googleUserName']);
		$_SESSION['avatarLink'] = $_POST['imgLinkGoogle'];
		$_SESSION['access_token'] =$_POST['id_token'];
		$value = $new_User->checkEmailHandleOAuth();
		
		if($new_User->checkEmailHandleOAuth() == 8 || $new_User->checkEmailHandleOAuth() == 10 ){

			try{
				///updata access_token used as password in the data base
				
			
				include 'dbconnect.php';
				$Email = stripslashes(htmlspecialchars($_POST['emailGoogle']));
				
				$queryUpdatePWD = $Connection->prepare("UPDATE Users SET userPassword=:temp WHERE userEmail=:userMtemp");
				$queryUpdatePWD->execute(array('temp' => $_SESSION['access_token'], 'userMtemp' => $Email));
				header("Location:Login.php?email=" .$_POST['emailGoogle'] ."&password=" .$_POST['id_token']."&g=1" );
				///echo "Already sign up just changing password<br> Confirming sign up<br>";
				//$ConFirm = $Connection->prepare("SELECT userEmail,userPassword FROM Users");
				//$ConFirm->execute();
				//$result = $ConFirm->fetchAll();
				//echo var_dump($result);
				//echo "yes";
			
			
				

			}catch(PDOException $e){
			 $e->getMessage();
			}
			
			
		}elseif($new_User->checkEmailHandleOAuth() == 1)
		{	
			try{
				$Name = stripslashes(htmlspecialchars($_POST['googleUserName']));
			$Email = stripslashes(htmlspecialchars($_POST['emailGoogle']));
			$Handle = stripslashes(htmlspecialchars($handle));
			$password = stripslashes(htmlspecialchars($_POST['id_token']));
			$new_User->setUserEmail($Email);
			$new_User->setUserFullname($Name);
			$new_User->setUserHandle($Handle);
			$new_User->setUserPassword($password);
			//$new_User->SignUpUserG($Name,$Email,$Handle,$password);
			$new_User->SignUpUser();
			header("Location:Login.php?email=" .$_POST['emailGoogle'] ."&password=" .$_POST['id_token']."&g=1" );
			}catch(Exception $e){
				$e->getMessage();
			}
			
			//echo "In check email Handle";
			
			//$ConFirm = $Connection->prepare("SELECT * FROM Users");
			//$ConFirm->execute();
			//$result = $ConFirm->setFetchMode(PDO::FETCH_ASSOC);
			//echo var_dump($result);
			//header("Location:Login.php?email=" .$_GET['useremail'] ."&password=" .$_SESSION['access_token']);
		}elseif ($new_User->checkEmailHandleOAuth() == 9) {
			# code...
			$value = 9;
			echo $value;
		}
		
	}

	if(isset($_GET['twitFullname']) && isset($_GET['twitHandle']) && isset($_GET['twitEmail']) && isset($_GET['PicLink'])){

		$new_User = new User();
	
		$new_User->setUserEmail($_GET['twitEmail']);
		$new_User->setUserHandle($_GET['twitHandle']);
		$new_User->setUserFullname($_GET['twitFullname']);
		$_SESSION['avatarLink'] = $_GET['PicLink'];
		$_SESSION['access_token'] ='root';


		$value = $new_User->checkEmailHandleOAuth();
		
		if($new_User->checkEmailHandleOAuth() == 8 || $new_User->checkEmailHandleOAuth() == 10 ){

				try{
				include 'dbconnect.php';
				$Email = stripslashes(htmlspecialchars($_GET['twitEmail']));

				$queryUpdatePWD = $Connection->prepare("UPDATE Users SET userPassword=:temp WHERE userEmail=:userMtemp");
				$queryUpdatePWD->execute(array('temp' => $_SESSION['access_token'], 'userMtemp' => $Email));
				header("Location:Login.php?email=" .$_GET['twitEmail'] ."&password=" .$_SESSION['access_token']."&t=1" );
				///echo "Already sign up just changing password<br> Confirming sign up<br>";
				/*$ConFirm = $Connection->prepare("SELECT userEmail,userPassword FROM Users");
				$ConFirm->execute();
				$result = $ConFirm->fetchAll();
				echo var_dump($result);*/
				echo "yes";
				}catch(PDOException $e){
				$e->getMessage();
				}
			
			
			
		}elseif($new_User->checkEmailHandleOAuth() == 1)
		{	
			try{
				$Name = stripslashes(htmlspecialchars($_GET['twitFullname']));
			$Email = stripslashes(htmlspecialchars($_GET['twitEmail']));
			$Handle = stripslashes(htmlspecialchars($_GET['twitHandle']));
			$password = stripslashes(htmlspecialchars($_SESSION['access_token']));
			$new_User->setUserEmail($Email);
			$new_User->setUserFullname($Name);
			$new_User->setUserHandle($Handle);
			$new_User->setUserPassword($password);
			/*$new_User->SignUpUserG($Name,$Email,$Handle,$password);*/
			$new_User->SignUpUser();
			header("Location:Login.php?email=" .$_GET['twitEmail'] ."&password=" .$_SESSION['access_token']."&t=1" );
			}catch(Exception $e){
				$e->getMessage();
			}
			
			//echo "In check email Handle";
			/*
			$ConFirm = $Connection->prepare("SELECT * FROM Users");
			$ConFirm->execute();
			$result = $ConFirm->setFetchMode(PDO::FETCH_ASSOC);
			echo var_dump($result);
			header("Location:Login.php?email=" .$_GET['useremail'] ."&password=" .$_SESSION['access_token']);*/
		}elseif ($new_User->checkEmailHandleOAuth() == 9) {
			# code...
			echo "You cant sigup someone has that handle already.... please change it from your screen name from twitter and try again";
			
		}
		

	}
	/*
	///wont be neccesary 
	Required takes care of these errors so no 
	submission unless all fields are completed
	else{
		if (empty($_POST['Fullname']))
		{
			header("Location:index2.php?error=2");  
		}elseif(empty($_POST['email'])){
		header("Location:index2.php?error=3");
		}elseif(empty($_POST['handle'])){
 		header("Location:index2.php?error=4");
		}elseif(empty($_POST['password'])){
		header("Location:index2.php?error=5");
		}else{
		header("Location:index2.php?error=6");
		}

	}
*/
/*
	error numbers meaning 
	 Required takes case of this errors so no 
	 submittion unless all field are completed
	 1
	 2 Full Name not inputed
	 3 email not inputed 
	 4 handle not inputed 
	 5 password not inputed
	 6 Confirmed password not inputed

     ///Definitely need this for important error handling
	 7 password and Cpassword not matching 
	 8 Email match to database
	 9 Handle mathc to database
	 10 Both Email and Handle to match to database
	 11 Failed authentication signup
*/

 ?>