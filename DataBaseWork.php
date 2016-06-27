<?php

class DataBaseWork
{
    private $mysqli;
    private $dbTableName;


    function __construct($dbTableName)
    {
        $this->dbTableName = $dbTableName;
    }

    public function connect($host, $dbLogin, $dbPassword, $dbName)
    {
        $this->mysqli = new mysqli($host, $dbLogin, $dbPassword, $dbName);

        if (mysqli_connect_errno())
        {
            die(mysqli_connect_error());
        }

        $this->mysqli->set_charset("cp1251");
    }

    public function createTable()
    {
        $checkTable = $this->mysqli->query("CHECK TABLE $this->dbTableName");
        $resultCheck = $checkTable->fetch_row();

        if($resultCheck[3] != 'OK')
        {

            if ($this->mysqli->query("CREATE  TABLE $this->dbTableName (id INT(3) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), name VARCHAR(255), start_date INT(10), finish_date  VARCHAR(255), status VARCHAR(255))") === FALSE)
            {
               printf('Failed to create tables - ' . $this->mysqli->error);
                die();

            }

        }
        else
        {
            $resTableCreate =  '“аблица ' . $this->dbTableName . ' была создана ранее';
        }

        return  $resTableCreate;
    }

    public function insert($openCsv)
    {
        $replaceSymbolDate = array(' ', '-', '.', ';', '/');
        foreach($openCsv as $key => $values)
        {
            $newDate =  str_replace($replaceSymbolDate, '', $values[2]);
            if(!($this->mysqli->query("INSERT INTO $this->dbTableName (id, name, start_date, finish_date, status) VALUES ('$values[0]', '$values[1]', '$newDate', '$values[3]', '$values[4]') ")))
            {
                printf('Failed to add records to the table - ' . $this->mysqli->error);
                die();
            }
        }
        return true;

    }

    //ћетод возвращает случайную строку из таблицы с измененным статусом
    public function changeStatus()
    {
        $countString = $this->mysqli->query("SELECT count(*) FROM $this->dbTableName");
        $randomStringNumber = rand(0, $countString->fetch_row()[0]);
        $modifiedRandomString = $this->mysqli->query("SELECT id, status FROM $this->dbTableName LIMIT $randomStringNumber, 1");
        $randomString = $modifiedRandomString->fetch_row();
        ($randomString[1] == 'Off') ? $newStatus = 'On' : $newStatus = 'Off';
        if(!$this->mysqli->query("UPDATE $this->dbTableName SET status = '$newStatus' WHERE id =  $randomString[0]"))
            die('Failed to update table - ' . $this->mysqli->error);

        $result = $this->mysqli->query("SELECT * FROM $this->dbTableName WHERE id = $randomString[0]");

        return $result->fetch_row();

    }


}