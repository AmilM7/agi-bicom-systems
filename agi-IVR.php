#!/usr/bin/php 

<?php
include('/var/lib/asterisk/agi-bin/phpagi-2.20/phpagi.php');
include('/var/lib/asterisk/agi-bin/functions.php');

$connectionDB = createConnection();
if(mysqli_connect_error()) {
    exit('Error connecting to the DB server');
}

$agi = new AGI();
$IDuser = $agi -> parse_callerid()['username'];
$agi -> answer();
$agi -> stream_file('im-sorry');
$agi -> stream_file('hello');

$time = date('H');
$timeInt = (int)$time;
if ($timeInt >= 0 and $timeInt < 12) {
    $agi -> stream_file('good-morning');
} elseif ($timeInt >= 12 and $timeInt < 18) {
    $agi -> stream_file('good-afternoon');
} else {
    $agi -> stream_file('good-evening');
}

$password = getPassword($connectionDB, $IDuser);
if(!checkPassword($agi, $password)) {
    exit();
}

$i = 0;
while ($i < 5) {
    $agi -> stream_file('welcome');
    $option = $agi -> get_data('available-options', 5000, 1)['result'];
    $agi -> say_number($option);
    sleep(0.5);

    switch ($option) {
        case 1:
            $ext = $agi -> get_data('vm-enter-num-to-call', 5000, 6)['result'];
            if($IDuser == $ext){
                $agi -> stream_file('invalid');
            } else {
                $agi -> exec_dial('PJSIP', $ext)['result'];
            }
            break 2;
        case 2:
            if(!checkPassword($agi, $password)) {
                break 2;
            }
            $newPassword1 = $agi -> get_data("vm-newpassword")['result'];
            sleep(0.5);
            $newPassword2 = $agi -> get_data("vm-reenterpassword")['result'];
            if($newPassword1 != $newPassword2) {
                $agi -> stream_file("wrong-try-again-smarty");
                break 2;
            } else {
                $query1 = 'CALL updatePass(' .$IDuser. ',' .$newPassword1. ');';
                if(!$connectionDB -> query($query1)){
                    $agi -> stream_file('connection-failed');
                    break 2;
                }
                else {
                    $agi -> stream_file('good');
                    break 2;
                }
            }
        case 3:
            $i+=1;
            break;
        default:
            break 2;
            $i+=1;
    }
}

$agi -> hangup();
$connectionDB -> close();

/*Procedure:
create procedure updatePass (IN username1 varchar(20), IN password1 varchar(20))
    -> begin
    -> update Users
    -> set
    -> password=password1 where username=username1;
    -> end;
    -> /*/
?>



