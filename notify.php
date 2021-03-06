<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();

     if(!isset($_SESSION['userId']) )
    {
        header("Location:index.php");
    }else
/*elseif(!isset($_SESSION['authenticationFlag'])){
         header("Location:2Fa.php");
    }*/
    $tempUserID = $_SESSION['userId'];
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" type="text/css" href="stylewelcome.css">
   
    <title>Get Together </title>

    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Our Custom CSS -->
<!--     <link rel="stylesheet" href="style2.css">
     -->    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js" integrity="sha384-kW+oWsYx3YpxvjtZjFXqazFpA7UP/MbiY4jvs+RWZo2+N94PFZ36T6TFkc9O3qoB" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>

 
</head>

<body>

    <div class="wrapper">
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
              
                <?php 

              if($_SESSION['userId'] == 6){
                include_once 'roomClass.php';
                $newRoomObj = new Room();
                echo '<li>
                <a href="#roomSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Rooms</a>
                <ul class="collapse list-unstyled" id="roomSubmenu">';
                 require 'dbconnect.php';
                $tempId = $_SESSION['userId'];
                ///used the Admin(user) Id to display the users list of rooms to them
                $query = $Connection->prepare("SELECT RoomsID, Name FROM Administrators INNER JOIN Rooms ON Administrators.RoomsID = Rooms.ID WHERE UserID=:tempUserId");
                $query->execute(array('tempUserId'=> $tempId));
                while( $result = $query->fetch()){


                echo '<li class="row"><a href="GlobalRoom.php?currentRoomID=' .$result['RoomsID'] .'&page=1' .'"  class="col-8" style="margin-left: 7%">' .$result['Name'] .'Room'.'</a>';

                  if($newRoomObj->check_room_status($result['RoomsID'] ) ==1){
                 echo '<i id="' .$result['RoomsID'] .'" class="fa fa-archive col-3" style="font-size:36px; color:black;"></i></li>'; 
                  }else{
                  echo '<i id="' .$result['RoomsID'] .'" class="fa fa-archive col-3" style="font-size:36px; color:red;"></i></li>'; 
                  }
              
                }
                $Connection = null;
                echo '</ul></li>';

            }else{  
                echo '<li>
                <a href="#roomSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Rooms</a>
                <ul class="collapse list-unstyled" id="roomSubmenu">
                <a href="#JoinSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Joined Room</a>
                <ul class="collapse list-unstyled" id="JoinSubmenu">';
               
                require 'dbconnect.php';
                $tempId = $_SESSION['userId'];
                ///used the user Id to display the users list of rooms to them
                $query = $Connection->prepare("SELECT RoomsID, Name FROM UserGroups INNER JOIN Rooms ON UserGroups.RoomsID = Rooms.ID WHERE UserID=:tempUserId");
                $query->execute(array('tempUserId'=> $tempId));
                while( $result = $query->fetch()){
                echo '<li><a href="GlobalRoom.php?currentRoomID=' .$result['RoomsID'] .'&page=1'.'">' .$result['Name'] .'Room'.'</a></li>'; 
                }
                $Connection = null;

                echo '</ul><a href="#ownSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Own Room</a>
                <ul class="collapse list-unstyled" id="ownSubmenu">';
                require 'dbconnect.php';
                $tempId = $_SESSION['userId'];
                ///used the Admin(user) Id to display the users list of rooms to them
                $query = $Connection->prepare("SELECT RoomsID, Name FROM Administrators INNER JOIN Rooms ON Administrators.RoomsID = Rooms.ID WHERE UserID=:tempUserId");
                $query->execute(array('tempUserId'=> $tempId));
                while( $result = $query->fetch()){
          echo '<li class="row"><a href="GlobalRoom.php?currentRoomID=' .$result['RoomsID'] .'&page=1' .'"  class="col-8" style="margin-left: 7%">' .$result['Name'] .'Room'.'</a></li>'; 
/*<i id="' .$result['RoomsID'] .'" class="fa fa-archive col-3" style="font-size:36px; color:black;"></i>*/

                }               
                $Connection = null;

               echo '</ul></ul></li>';
              }
              echo '<li> <a href="upload.php">Upload Picture</a></li>';              
              ?>
              <li>
              <a href='profile.php?userId=<?php echo $_SESSION['userId'] ?>'>View My Profile</a>
              </li>
        </nav>
        <!-- Page Content  -->
        <div id="content">

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
            <a href=""></a>
   
                <?php 
                include 'dbconnect.php';
                $flag = 0; $tempUserID = $_SESSION['userId'];
                $Sql = $Connection->prepare("SELECT  AdminID,RoomID,link,flag,userFullName ,Name FROM InviteLinks 
                INNER JOIN Users ON InviteLinks.AdminID =Users.userId
                INNER JOIN Rooms ON InviteLinks.RoomID =Rooms.ID
                WHERE flag=:tempFlag AND InviteLinks.userID=:tempUserId");
                $Sql->execute(array('tempFlag' => $flag, 'tempUserId' => $tempUserID));
                $count  = $Sql->rowCount();
                if($Sql->rowCount() > 0)
                {
                echo "You have " . $count ." New Room Chat Invite" ."<br>"; 
                echo '<div class="line"></div>';
                    while ( $result = $Sql->fetch()) {
                        # code...
                        echo '<div>
                            You Have received an Invitation from <b>' .$result['userFullName'] .'</b> To Join the room called <b style="blue">' .$result['Name'] .'</b><br>'
                            .'Click <a href="' .$result['link'].'&roomID=' .$result['RoomID'] .'&accept=1' .'">' .'<b>Here</b></a>' .' to Join' .' Or Click <a href="' .$result['link'] .'&roomID=' .$result['RoomID'] .'&accept=0' .'">' .'<b>Here</b></a>'  .'To turn down Invitation'
                         .'</div><br>';
                    }
                }else{
                     echo "You have No New Room Chat Invite"; 
                }
                $Connection = null;

                ?>
            </div>
            

    <!-- jQuery CDN - Slim version (=without AJAX) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    <!-- jQuery Custom Scroller CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
    
    
    <!-- For dialog box -->
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.11.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css" />
    <script type="text/javascript" src="toggle.js"></script>
</body>

</html> 
