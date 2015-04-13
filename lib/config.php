<?php

//START OF METHODS AND FUNCTIONS GENERAL
function arrayToString($name, $array) {
    return implode($array[$name]);
}

//function checkEmptyString ($array){

function checkParametersReport($array) {
    //If Expression below is true then Error
    if (empty($array["address"]) || empty($array["reporter_last_name"]) || empty($array["reporter_first_name"]) || empty($array["reporter_middle_name"]) || empty($array["reporter_contact_number"]) || empty($array["reporter_email_address"])) {
        return false;
    }
    return true;
}

function checkParametersRecovery($array) {
    if (empty($array["est_damages"]) || empty($array["families_affected"])) {
        return false;
    }
    return true;
}

function checkParametersOwner($array) {
    if (empty($array["owner_last_name"]) || empty($array["owner_first_name"]) || empty($array["owner_middle_name"]) || empty($array["owner_contact_number"]) || empty($array["owner_email_address"])){
        return false;
    }
    return true;
}

function checkParametersLogIn($data){
    if (empty($data["email"]) || empty($data["password"])){
        return false;
    }
    return true;
}

function checkParameterCookie($cookie){
    if (empty($cookie["authorization_user_name"]) || empty($cookie["authorization"])){
        return false;
    }
    return true;
}
