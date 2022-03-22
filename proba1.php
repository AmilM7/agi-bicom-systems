#!/usr/bin/php 

<?php
include('/var/lib/asterisk/agi-bin/phpagi-2.20/phpagi.php');

//Attributes for connection
$host = "localhost";
$user = "root";
$passwordDB = "";
$DB = "asterisk";

$connectionDB =  new mysqli($host, $user, $passwordDB, $DB);
if(mysqli_connect_error()) {
    exit('Error connecting to the DB server');
}

$agi = new AGI();
$IDuser = (string) $agi->parse_callerid()['username'];

$agi->answer();
$agi->stream_file('im-sorry');   // First  stream_file that does not work
$agi->stream_file('hello');

$vrijeme1 = date('H');
$vrijeme = (int)$vrijeme1;
if ($vrijeme>=0 and $vrijeme<12) {
    $agi->stream_file('good-morning');
}
elseif($vrijeme>=12 and $vrijeme<18) {
    $agi->stream_file('good-afternoon');
}
elseif ($vrijeme>=18 and $vrijeme<=23) {
    $agi->stream_file('good-evening');
}

/*
$query1 = 'select password from Users where username = "' . $IDuser . '";';    // ovaj kod je komentiran iz razloga da bih mogao pokazati prvobitni nacin na koji sam radio
$queryPassword = $connectionDB -> query($query1);
$password = (string) $queryPassword -> fetch_assoc()['password'];
/*
if($password==NULL){
    $query2="insert into Users (username,password) values ('" . $IDuser . "','x');";  // promijeni insert, radi samo update
    if(!$connectionDB -> query($query2)){
        $agi->stream_file('connection-failed');
    }
}*/


$query1 = 'select password from Users where username = "' . $IDuser . '";';
$queryPassword = $connectionDB -> query($query1);
$password =$queryPassword -> fetch_assoc()['password'];


if (!is_null($password)) {
    $enteredPassword  = (string) $agi->get_data("enter-password",8000,5) ['result'];
if ($enteredPassword!= (string) $password) {
    $agi-> stream_file("sorry_login_incorrect");
    exit();
}
}

$i = 0;

for(;;) {
    $agi->stream_file('welcome');
    $option = (integer) $agi->get_data('available-options',5000,1) ['result'];
    $agi->say_number($option);
    sleep(0.5);

    if($option==1) {
        $ext = (integer) $agi->get_data('vm-enter-num-to-call',5000,6) ['result'];
        if($IDuser==$ext){
            $agi->stream_file('invalid');
        } else {
            $agi->exec_dial('PJSIP',$ext)['result'];
        }
        break;
        }
    elseif ($option==2) {
        if (!is_null($password)) {
            $enteredPassword  = (string) $agi->get_data("enter-password",8000,5) ['result'];
            if ($enteredPassword!= (string) $password) {
                $agi-> stream_file("not_pass");
                break;
            }
        }
        $newPassword1 = (string) $agi->get_data("vm-newpassword")['result'];
        sleep(0.5);
        $newPassword2 = (string) $agi->get_data("vm-reenterpassword")['result'];
        if($newPassword1!=$newPassword2) {
            $agi-> stream_file("wrong-try-again-smarty");
            break;
        }
        else {
            $query1='update Users set password = "' . $newPassword1 . '" where username = "' . $IDuser . '";';
            if(!$connectionDB -> query($query1)){
                $agi->stream_file('connection-failed');
                break;
            }
            else {
                $agi->stream_file('good');
                break;
            }
        }
    }
    elseif ($option==3) {
        $i+=1;
    }
    elseif ($option==4) {   // if user presses 4, than it will get out
        break;
    }
    elseif ($option>4) {
        $agi->stream_file('error-number');
        break;
    }

    if($i==4) {   // this is made if user constantly is pressing option 3 to repeat IVR, after 3 attempt, a user will be interrupted
        break;
    }
}

$connectionDB ->close();
$agi->hangup();
?>
