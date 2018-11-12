<?php
     ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    session_start();
    $_SESSION['currentRoomID'] = $_GET['currentRoomID'];
    $_SESSION['currentPage']   = $_GET['page'];
    echo $_SESSION['currentPage'] ;
   
    echo $_SESSION['userId'];
   if(!isset($_SESSION['userId']))
    {
        header("Location:index.php");
    }else

    $tempUserID = $_SESSION['userId'];
   
function likes_dislike_Post($rowID){
     include_once 'rating.php'; 
    $tempUserID = $_SESSION['userId'];

   if(userLiked($rowID,$tempUserID)){
      $likesS =   'class="fa fa-thumbs-up like-btn" style="font-size:36px"';
   }else{
     $likesS =   'class="fa fa-thumbs-o-up like-btn" style="font-size:36px"';
   }

    $likesS .=  ' data-id="' .$rowID .'" data-user="' .$tempUserID .'"';

   if(userDisliked($rowID,$tempUserID)){
      $dislikeS = 'class="fa fa-thumbs-down dislike-btn" style="font-size:36px"';
   }else{
       $dislikeS = 'class="fa fa-thumbs-o-down dislike-btn" style="font-size:36px"';
   }
   
    $dislikeS .=  ' data-id="' .$rowID .'" data-user="' .$tempUserID .'"';
  
    echo '<div class="col">';
    echo '<i ' .$likesS .'></i>';
    echo '<span class="col likes">';
    echo getusersLikes($tempUserID);
    echo '</span>';
    echo '<i ' .$dislikeS .'></i>';
    echo '<span class="col dislikes">';
    echo getusersDislikes($tempUserID);
    echo '</span>';
    echo '</div>';
    /*end of first col*/
    
    echo '<div class="col">';
    echo '<button id="'.$rowID .'" type="button" style="float:right"class="btn btn-success view" >Comment</button>';

    echo '</div>';
    /*end of row */
}

function roomName_querry(){
    include 'dbconnect.php';

    $tempUserCRID =  $_SESSION['currentRoomID'];
    $querrystatement = "SELECT Name FROM Rooms WHERE ID=:roomID";
    $roomNamequerry=$Connection->prepare($querrystatement);
    $roomNamequerry->execute(array('roomID' => $tempUserCRID));
    
    $roomNameData = $roomNamequerry->fetch();
    $Connection = null;
    $roomName = $roomNameData['Name'];
    echo "You are not of the room called " .$roomName;
}

function sql_max_chat_per_room(){
    include 'dbconnect.php';
    $currentRoomChatID =  $_GET['currentRoomID'];
    $SQL ="SELECT * FROM ChatBox WHERE RoomID=:roomIdentify";
    $query = $Connection->prepare($SQL);
    $query->execute(array('roomIdentify' => $currentRoomChatID));
    $result = $query->rowCount();
    $Connection = null;
    return $result;
}

function postArea(){
      echo '<div id="container">
      <form id="chatArea" style="margin-top: 13%" action="post.php" method="post">
      <label for="messages">YourMessage:</label>
      <div class="form-group ">        
      <textarea class="form-control" rows="2" id="messages" name="messages" form="chatArea" maxlength="400" placeholder="Write a Post"></textarea>
      <span ><button  type="submit" class="btn btn-success" style="margin-top: 4px;">Send</button></span> 
      </div>
      </form>
      </div>';
}
function commentArea($rowID){
      echo '<div class="input-group reply"><textarea id="' .'texArea' .$rowID  .'" class="form-control custom-control" rows="1" style="resize:none" placeholder="Write a Comment"></textarea>';   
      echo '<span id="' .'send'.$rowID .'" class="input-group-addon btn btn-primary Comment" data-userId="'.$_SESSION['userId'] .'" >Send</span></div>';
}

function sql_fecth_post($maxpostsize){
          //Querry for text,user handle and time stamp
        $currentRoomChatID = $_SESSION['currentRoomID'];
        $currentPage = $_SESSION['currentPage'];
    

        $StopCheck = $maxpostsize % 5;
        $Stop = 5;
	
		switch ($StopCheck) {
			case 0:
				
				if ($currentPage == 1){
				$Start = 0;
				}else{
				$Start = ($currentPage - 1)* 5;			
				}
			break;

			case 1:
				
				if ($currentPage == 1){
				$Start = 0;
				$Stop = $StopCheck; ///equals to 1
				}else{

					
				$Start = (($currentPage -1)*5) - 4;
				$Stop = 5;
				}
			break;

			case 2:
				if ($currentPage == 1){
				$Start = 0;
				$Stop = $StopCheck; ///equals to 1
				}else{

					
				$Start = (($currentPage -1)*5) - 3;
				$Stop = 5;
				}
			break;

			case 3:if ($currentPage == 1){
				$Start = 0;
				$Stop = $StopCheck; ///equals to 1
				}else{

					
				$Start = (($currentPage -1)*5) - 2;
				$Stop = 5;
				}
			break;

			case 4:
			if ($currentPage == 1){
				$Start = 0;
				$Stop = $StopCheck; ///equals to 1
				}else{

					
				$Start = (($currentPage -1)*5) - 1;
				$Stop = 5;
				}

			break;

		}





		/*if ($currentPage == 1){
			$Start =0;
			if(!($maxpostsize % 5 == 0)){
				$Stop  = $maxpostsize % 5;
				$Newstop = $Stop;
				echo "New stop" .$Newstop .'<br>';
			}
		}else{
	$Start = ($currentPage - 1)* 5;			
	echo "New stop" .$Newstop .'<br>';
		$Start = $Stop;
		}
		*/

      /*  if ($currentPage == 1){

        	$Start =0;
        	if(!($maxpostsize % 5 == 0)){
				$Stop  = $maxpostsize % 5;

			}
        	
        }
        */
     	
        include 'dbconnect.php';
        $SQL ="SELECT ID, created_at, TextA, ChatBox.UserID, userHandle FROM ChatBox 
                    INNER JOIN Users ON ChatBox.UserID = Users.userID WHERE RoomID=:roomIdentify ORDER BY ID DESC LIMIT :start ,:stop";
        $query = $Connection->prepare($SQL);
        $query->bindParam(':start', $Start, PDO::PARAM_INT);
        $query->bindParam(':stop', $Stop, PDO::PARAM_INT);
        $query->bindParam(':roomIdentify', $currentRoomChatID, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetchAll();
        $Connection = null;

        return $result;
}

function sql_post_profilePic($UserID){
        include 'dbconnect.php';

        $querryProfilPic= $Connection->prepare("SELECT * FROM ProfilePictures WHERE userID=:tempId");
        $querryProfilPic->execute(array('tempId' => $UserID));

        $PicLinkResult = $querryProfilPic->fetch();
        if($PicLinkResult['userId'] ==  $UserID){
          echo '<img  src="' .$PicLinkResult['pictureLink']  .'" alt="Smiley face" style="float:right" width="42" height="42"><br><br><br><div>';
        }else{
          echo '<img  src="../ProfilePics/james.jpeg" alt="Smiley face" style="float:right" width="42" height="42"><br><br><br><div>';
        }

}

function sql_fetch_comment(){
      include 'dbconnect.php';

      
      $currentRoomChatID = $_SESSION['currentRoomID'];

     $sqlComment ="SELECT C1.`Id`as Id,`ChatBox`.`ID` as ID,
        `ChatBox`.`created_at`,
        `ChatBox`.`TextA`,
        `ChatBox`.`UserID`,
        t2.`userHandle` as t2userHandle,
        C1.`userId` as userId,
        C1.`TextArea` as TextArea,
        C1.`Ccreated_at` as Ccreated_at,
        t3.`userHandle` as t3userHandle
        FROM `ChatBox` 
        INNER JOIN `Users` AS t2 ON `t2`.`UserID` = `ChatBox`.`userID`
        INNER JOIN `Comment` AS C1 ON `ChatBox`.`ID` = `C1`.`ChatBoxID` 
        INNER JOIN `Users` AS t3 ON `t3`.`UserID` = `C1`.`userId` 
        WHERE `RoomID`=:roomIdentify
        ORDER BY C1.`Id` DESC";
        /*$sqlComment = "SELECT TextArea, ChatBoxID, Comment.userId AS userComment, Ccreated_at FROM Comment ORDER BY Id";*/
        $query = $Connection->prepare($sqlComment);
        $query->execute(array('roomIdentify' => $currentRoomChatID));
        $resultComment = $query->fetchAll();
        $Connection = null;

        return $resultComment;
}


function printPagePanel($maxPageSize)
{
  $root = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  $page = 1;
  if( isset($_GET['page']) )
  {
    $root = str_replace('&page='.$_GET['page'], '', $root);
    $page = $_GET['page'];
  }

  $pages = pagination($page, $maxPageSize);
 
  echo '<div style="text-align:center; font-weight: bold; font-size:16px; padding-bottom: 5px;">';
  for($i = 0; $i<count($pages); $i++)
  {
    if( $pages[$i] == -1  )
    {
      echo '<span>...</span>';
     /* echo '<a href="' . $root . '&page='.$maxPageSize. '">&nbsp;'. $pages[$i] .'&nbsp;</a>&nbsp;';*/
    }
    else
    {
      echo '<a href="' . $root . '&page='. $pages[$i] . '">&nbsp;'. $pages[$i] .'&nbsp;</a>&nbsp;';
    }
  }
  echo '</div>';  
}

//credit: https://gist.github.com/kottenator/9d936eb3e4e3c3e02598
function pagination($c, $m) 
{
    $current = $c;
    $last = $m;
    $delta = 2;
    $left = $current - $delta;
    $right = $current + $delta + 1;
    $range = array();
    $rangeWithDots = array();
    $l = -1;

    for ($i = 1; $i <= $last; $i++) 
    {
        if ($i == 1 || $i == $last || $i >= $left && $i < $right) 
        {
            array_push($range, $i);
        }
    }

    for($i = 0; $i<count($range); $i++) 
    {
        if ($l != -1) 
        {
            if ($range[$i] - $l === 2) 
            {
                array_push($rangeWithDots, $l + 1);
            } 
            else if ($range[$i] - $l !== 1) 
            {
              //-1 is used to mark ...
                array_push($rangeWithDots, -1);
            }
        }
        
        array_push($rangeWithDots, $range[$i]);
        $l = $range[$i];
    }

    return $rangeWithDots;
}


/*function comment_button*/
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" type="text/css" href="stylewelcome.css">
    <title>Get Together </title>

<!--   Bootstrap CSS CDN-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
<!-- Our Custom CSS-->
   <link rel="stylesheet" href="style2.css">
<!--    Scrollbar Custom CSS-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
<!--    Font Awesome JS-->
   <!-- <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js" integrity="sha384-kW+oWsYx3YpxvjtZjFXqazFpA7UP/MbiY4jvs+RWZo2+N94PFZ36T6TFkc9O3qoB" crossorigin="anonymous"></script>
   <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script> -->
<!-- Thumps Up thump down -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   
<style>
  @media (min-width: 1200px) {
    .post{
        max-width: 100%;
    }

}

#timestamp{
  color: red;
  background-color:blue;
}
.posts-wrapper {
  width: 70%;
  margin: 20px auto;
  border: 1px solid #eee;
}
.comment-wrapper {
  width: 90%;
  margin: 20px auto;
  border: 1px solid #eee;
}
.comment-div {
  width: 100%;
  margin-bottom: 0px;
  margin-right: 0px;
  margin-left: 0px;
  margin-top: 30px;
  border: 1px solid #eee;
}

.post {
  width: 90%;
  margin: 20px auto;
  padding: 10px 5px 0px 5px;

}
.post-info {
  margin: 10px auto 0px;
  padding: 5px;
}
.fa {
  font-size: 1.2em;
}
.fa-thumbs-down, .fa-thumbs-o-down {
  transform:rotateY(180deg);
}
.logged_in_user {
  padding: 10px 30px 0px;
}
i {
  color: blue;
}
    </style>
</head> 
<body>

        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3 style="color: orange; font-family: tahoma; font-size: 25px;">Welcome 
                    <span>
                        <?php echo $_SESSION['userName'];?>
                            
                        </span>
                </h3>
        
            </div>
        
            <!-- <ul class="list-unstyled components"> -->
            <ul class="list-unstyled CTAs" >
                <p></p>
                <li>
                        <a href="Welcome.php">Home</a>
                    </li>
                <li>
                        <a href="notify.php">Notification</a>
                </li>
                    <li>
                        <a href="#roomSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Rooms</a>
                        <ul class="collapse list-unstyled" id="roomSubmenu">
                        <a href="#JoinSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Joined Room</a>
                        <ul class="collapse list-unstyled" id="JoinSubmenu">
                            <?php 
                        require 'dbconnect.php';
                        $tempId = $_SESSION['userId'];
                        ///used the user Id to display the users list of rooms to them
                        $query = $Connection->prepare("SELECT RoomsID, Name FROM UserGroups INNER JOIN Rooms ON UserGroups.RoomsID = Rooms.ID WHERE UserID=:tempUserId");
                        $query->execute(array('tempUserId'=> $tempId));
                while( $result = $query->fetch()){
                    echo '<li><a href="GlobalRoom.php?currentRoomID=' .$result['RoomsID'] .'&page=1'.'">' .$result['Name'] .'Room'.'</a></li>'; 
                }
                    $Connection = null;

                             ?>
                        </ul>

                        <a href="#ownSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Own Room</a>
                        <ul class="collapse list-unstyled" id="ownSubmenu">
                        <?php 
                        require 'dbconnect.php';
                        $tempId = $_SESSION['userId'];
                        ///used the Admin(user) Id to display the users list of rooms to them
                        $query = $Connection->prepare("SELECT RoomsID, Name FROM Administrators INNER JOIN Rooms ON Administrators.RoomsID = Rooms.ID WHERE UserID=:tempUserId");
                        $query->execute(array('tempUserId'=> $tempId));
                while( $result = $query->fetch()){
                    echo '<li><a href="GlobalRoom.php?currentRoomID=' .$result['RoomsID'] .'&page=1' .'">' .$result['Name'] .'Room'.'</a></li>'; 
                }
                    $Connection = null;

                             ?>
                        </ul>
                        </ul>
                    </li>
                    <li>
                        <a href="profile.php">View My Profile</a>
                    </li>
                    <?php 
                        require 'dbconnect.php';
                        $tempId = $_SESSION['userId'];
                        $query = $Connection->prepare("SELECT * FROM Administrators WHERE UserID=:tempUserId AND RoomsID=:tempRoomID");
                        $query->execute(array('tempUserId'=> $tempId,'tempRoomID' => $_SESSION['currentRoomID']));
                        $result = $query->fetch();
                if($query->rowCount() > 0){
                    echo '<li><a href="sendInvitation.php?currentRoomID=' .$result['RoomsID'] .'">' .'Invite' .'</a></li>'; 
                }
                    $Connection = null;

                             ?>
            </ul>
        </nav>

        <!-- Page Content  -->
        <div id="content" style="position: fixed;">

            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">

                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-align-left"></i>
                        <span>More Information</span>
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>
                </div>
            </nav>
            
            <div class="line"></div>
            <?php 

          
            ?>
            <div id="container" >
              <?php 
            include 'dbconnect.php';
            include 'ChatClass.php';
            $dispUser = new Chat;
            $tempUserCRID = $_SESSION['currentRoomID'];
              if($dispUser->matchCheck($_SESSION['userId'],$tempUserCRID) == true)
              {
                     
                $postID = 0;
                $maxpage = sql_max_chat_per_room();
                $post = sql_fecth_post($maxpage);

                switch ($maxpage) {
                	case $maxpage < 5:
                		$PanelSize = 1;
                		break;
                	case $maxpage > 4:
                	$PanelSize =  round(($maxpage / 5));
                	break;
                	
                }
         		

                if (!empty($post)){
                printPagePanel($PanelSize);
                /*printPagePanel(5);*/
                echo '<div id="display" class="pre-scrollable">';
                foreach ($post as $row) {
                # code...
                $postID++; 
                echo ' <div id="' .$postID .'" class="posts-wrapper row">'; 
         
                /* echo '<div class="row"';*/
                echo '<div class="col-sm-10" style="background-color:lavender;">';
                echo "<p> {$row['TextA']}</p>";
                echo '<p style="background-color:blue;"' .'>' .$row['created_at'] .'</p>';


                echo '</div>';/* end of col-sm- 10*/

                echo '<div class="col-sm-2">';

                sql_post_profilePic($row['UserID']);
                echo $row['userHandle'];       echo $row['ID'];
                echo '</div><br></div>'; /*end o* col-sm-2*/

                likes_dislike_Post($row['ID']);
                $comment = sql_fetch_comment();
                echo '<div   id="'.'div'.$row['ID'] .'" class="comment-div row" style="display: none" >';
                echo '<div " class="comment-wrapper row">';

                foreach($comment as $value){

                  if($value['ID'] == $row['ID'] ){

                  if (!empty($comment)){
                    
                  echo '<div class="col-sm-10" style="background-color:lavender;">';
                  echo "<p> {$value['TextArea']}</p>";
                  echo '<p style="background-color:blue;"' .'>' .$value['Ccreated_at'] .'</p>';
                  echo '</div>';/* end of col-sm- 10*/
                  echo '<div class="col-sm-2">';
                  sql_post_profilePic($value['userId']);  ///reused for comment picture
                  echo $value['t3userHandle']; 
                  echo '</div><br></div>'; /*end o* col-sm-2*/
                  }

                  }

                }
                /* echo '';
                echo '';
                echo  '';*/
                /* Comment Section */

                commentArea($row['ID']);
                echo '</div></div><div></div></div>';/*row  and post warrapper*/
                }
                echo "</div>";


                }else{
                  echo "Welcome to link, link with other people in the room by chatting <br>";
                }

            }else{
              roomName_querry();
            }
              postArea();
            ?>      
       </div>   <!-- End of Container -->

    </div><!-- End ofPage Content  -->
    <!-- jQuery CDN - Slim version (=without AJAX) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

    <!-- jQuery Custom Scroller CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
     <script type="text/javascript" src="toggle.js"></script>
    <script type="text/javascript" src="jquery.js"></script>
   <script type="text/javascript" src="load.js"></script>
    <script type="text/javascript" src="rating.js"></script>
    <script type="text/javascript" src="comment.js"></script>

</body>

</html> 