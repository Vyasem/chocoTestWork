<?php
//Автоматическое подеключение файла с нужным классом
function __autoload($className)
{
    $includeFile = "{$className}.php";
    if(file_exists($includeFile))
    {
        require_once($includeFile);
    }
}

$host = 'localhost';
$dbLogin = 'root';
$dbPassword = '';
$dbName = 'chocotest';
$dbTableName = 'chocotable';

//Создаём экземпляр класса для работы с БД
$dbWork = new DataBaseWork($dbTableName);
$dbWork -> connect($host, $dbLogin, $dbPassword, $dbName);
$tableCreate = $dbWork -> createTable();
if($tableCreate == true)
	$tableCreate = 'Таблица ' . $dbTableName . ' была создана ранее';

if(!empty($_FILES['export']['name']))
{
    //Загрузка и экспорт файла в БД
    $handlerCsv = new csvHandler($_FILES['export']['tmp_name']);
    $handlerCsv->export();
    $handlerCsv->printLink();

    $openCsv = $handlerCsv->resultCsv;
    $dbWork->insert($openCsv);

    $linkList = $handlerCsv->arLinkList;


    $changestatus = $dbWork->changeStatus();
    $resultat = $handlerCsv->recoveryDateFormat($changestatus[2]);
    $changestatus[2] = $resultat;
    $modifiedString = 'Была изменена случайная запись в БД - ' . implode(';',$changestatus);



}

?>

<!DOCTYPE html>
<html>

    <head>
        <title>Тестовое задание</title>
        <meta http-equiv="Content-Type" content="text/html; charset=cp1251">
    </head>
    <body>
        <h2><?=$tableCreate?></h2>
        <h3><?=$modifiedString?></h3>
        <?if(isset($linkList)){?>
        <h3>Ссылки на записи:</h3>
        <?foreach($linkList as $key => $values){?>
            <h5><a href="/<?=$values?>"><?=$values?></a></h5>
        <?}?>
        <?}?>
        <form method="post" action="" enctype="multipart/form-data">
            <input type="file" name="export"><br><br>
            <input type="submit" value="Экспортировать">
        </form>
     </body>
</html>
