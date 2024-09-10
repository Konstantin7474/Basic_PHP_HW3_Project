<?php

// function readAllFunction(string $address) : string {
function readAllFunction(array $config) : string {
    $address = $config['storage']['address'];

    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "rb");
        
        $contents = ''; 
    
        while (!feof($file)) {
            $contents .= fread($file, 100);
        }
        
        fclose($file);
        return $contents;
    }
    else {
        return handleError("Файл не существует");
    }
}

// function addFunction(string $address) : string {
function addFunction(array $config) : string {
    $address = $config['storage']['address'];

    $name = readline("Введите имя и фамилию: ");
    $date = readline("Введите дату рождения в формате ДД-ММ-ГГГГ: ");


    //////
    if(!validateDate($date)){
        return handleError("Не верный формат даты!");
    }

    if(!validateName($name)){
        return handleError("Не верный формат имени!");
    }
    /////
    $data = "$name, $date" . PHP_EOL;

    $fileHandler = fopen($address, 'a');

    if(fwrite($fileHandler, $data)){
        return "Запись $data добавлена в файл $address"; 
    }
    else {
        return handleError("Произошла ошибка записи. Данные не сохранены");
    }

    fclose($fileHandler);
}

// function clearFunction(string $address) : string {
function clearFunction(array $config) : string {
    $address = $config['storage']['address'];

    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "w");
        
        fwrite($file, '');
        
        fclose($file);
        return "Файл очищен";
    }
    else {
        return handleError("Файл не существует");
    }
}

function helpFunction() {
    return handleHelp();
}

function readConfig(string $configAddress): array|false{
    return parse_ini_file($configAddress, true);
}

function readProfilesDirectory(array $config): string {
    $profilesDirectoryAddress = $config['profiles']['address'];

    if(!is_dir($profilesDirectoryAddress)){
        mkdir($profilesDirectoryAddress);
    }

    $files = scandir($profilesDirectoryAddress);

    $result = "";

    if(count($files) > 2){
        foreach($files as $file){
            if(in_array($file, ['.', '..']))
                continue;
            
            $result .= $file . PHP_EOL;
        }
    }
    else {
        $result .= "Директория пуста \r\n";
    }

    return $result;
}

function readProfile(array $config): string {
    $profilesDirectoryAddress = $config['profiles']['address'];

    if(!isset($_SERVER['argv'][2])){
        return handleError("Не указан файл профиля");
    }

    $profileFileName = $profilesDirectoryAddress . $_SERVER['argv'][2] . ".json";

    if(!file_exists($profileFileName)){
        return handleError("Файл $profileFileName не существует");
    }

    $contentJson = file_get_contents($profileFileName);
    $contentArray = json_decode($contentJson, true);

    $info = "Имя: " . $contentArray['name'] . "\r\n";
    $info .= "Фамилия: " . $contentArray['lastname'] . "\r\n";

    return $info;
}
//////////////////////////////////
function searchFunction(array $config): string{
    $address = $config['storage']['address'];

    if(!file_exists($address) || !is_readable($address)){
        return handleError("Файл не существует или не доступен для чтения");
    }

    $searchQuery = readline("Введите имя или дату для поиска (ДД-ММ-ГГГГ): ");
    $file = fopen($address, "r");
    $results = "";

    while(($line = fgets($file)) !== false) {
        $data = explode(", ", trim($line));
        if(count($data) === 2){
            $name = $data[0];
            $date = $data[1];

            if(stripos($name, $searchQuery) !== false || $date === $searchQuery){
                $results .= $line . PHP_EOL;
            }
        }
    }

    fclose($file);

    if($results) {
        return "Найдено:\n" . $results;
    } else {
        return "Совпадений ненайдено";
    }
}

////////////////////////////////////

function deleteFunction(array $config): string{
    $address = $config['storage']['address'];

    if(!file_exists($address) || !is_readable($address)){
        return handleError("Файл не существует или не доступен для чтения");
    }

    $searchQuery = readline("Введите имя или дату для удаления (ДД-ММ-ГГГГ): ");
    $file = fopen($address, "r");
    $tempFile = fopen($address . ".tmp", "w");
    $deleted = false;

    while(($line = fgets($file)) !== false) {
        $data = explode(", ", trim($line));
        if(count($data) === 2){
            $name = $data[0];
            $date = $data[1];

            if(stripos($name, $searchQuery) !== false || $date === $searchQuery){
                $deleted = true;
                continue;
            }
        }
        fwrite($tempFile, $line);
    }

    fclose($file);
    fclose($tempFile);


    if($deleted) {
        rename($address . ".tmp", $address);
        return "Записи, содержащие '$searchQuery', были удалены.";
    } else {
        unlink($address . ".tmp");
        return "Совпадений не найдено";
    }
}
//////////////////////////////



