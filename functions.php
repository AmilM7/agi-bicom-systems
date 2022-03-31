<?php

function createConnection() {
    $connectionDB = new mysqli("localhost", "root", "", "asterisk");
    return $connectionDB;
}

function getPassword ($connectionDB, $IDuser) {
    $query1 = 'SELECT password FROM Users WHERE username = "' .$IDuser. '";';
    $queryPassword = $connectionDB -> query($query1);
    return $queryPassword -> fetch_assoc()['password'];
}

function checkPassword(AGI $agi, $realPassword) {
    if (!is_null($realPassword)) {
    $enteredPassword = $agi -> get_data("enter-password", 8000, 5)['result'];
        if ($enteredPassword != $realPassword) {
            $agi -> stream_file("sorry_login_incorrect");
            return false;
        } else {
            return true;
        }
    }
}

?>
