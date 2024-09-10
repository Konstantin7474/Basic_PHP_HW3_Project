<?php



function validateDate(string $date): bool {
    $dateBlocks = explode("-", $date);

    if(count($dateBlocks) < 3){
        return false;
    }

    if(isset($dateBlocks[0]) && $dateBlocks[0] > 31) {
        return false;
    }

    if(isset($dateBlocks[1]) && $dateBlocks[1] > 12) {
        return false;
    }

    if(isset($dateBlocks[2]) && $dateBlocks[2] > date('Y')) {
        return false;
    }

    return true;
}

function validateName(string $name): bool {
    

    if(strlen($name) < 2){
        return false;
    }

    if(!preg_match("/^[a-zA-Za-яА-Я\s]+$/u", $name)){
        return false;
    }

    $nameParts = explode(" ", trim($name));
    if(count($nameParts) < 2){
        return false;
    }

    return true;
}