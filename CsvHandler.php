<?php

class csvHandler
{
    public $resultCsv;
    public $arLinkList = array();
    private $fileLink;

    function __construct($fileLink)
    {
        $this->fileLink = $fileLink;
    }


    public function export()
    {
        $row = -1;
        if (($handle = fopen($this->fileLink, 'r')) !== false)
        {
            while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                $num = count($data);

                if ($row != -1)
                {
                    for ($c = 0; $c < $num; $c++)
                    {
                        $this->resultCsv[$row][$c] = $data[$c];
                    }
                }
                $row++;
            }
        }

        return $this->resultCsv;
    }


    //Метод возвращает массив строк для ссылок
    public function printLink()
    {

        $arRulesTranslit = array(
                    "а" => "a", "ый" => "iy", "ые" => "ie", "б" => "b", "в" => "v",
                    "г" => "g", "д" => "d",   "е" => "e", "ё" => "yo", "ж" => "zh",
                    "з" => "z", "и" => "i",   "й" => "y", "к" => "k",  "л" => "l",
                    "м" => "m", "н" => "n",  "о" => "o", "п" => "p",  "р" => "r",
                    "с" => "s", "т" => "t",  "у" => "u", "ф" => "f",  "х" => "kh",
                    "ц" => "ts","ч" => "ch", "ш" => "sh", "щ" => "shch", "ь" => "",
                    "ы" => "y", "ъ" => "", "э" => "e", "ю" => "yu", "я" => "ya",
                    "йо" => "yo", "ї" => "yi", "і" => "i", "є" => "ye", "ґ" => "g",
                     "." => "-", "," => "", "!" => "-", "?" => "-", " " => "-",
                      ";" => "/", ":" => "-");

        foreach($this->resultCsv as $key => $val)
        {

            $tempVar = implode(";", $val);
            //преобразует символы в нижний регистр
            $tempVar = mb_strtolower($tempVar, 'cp-1251');

            for($i = 0; $i < iconv_strlen($tempVar); $i++)
            {
                //Если текущий и следующий за ним символы одновременно присутствуют в массиве, то они заменяются на соответствующие значения.
               if($arRulesTranslit[$tempVar[$i] . $tempVar[$i + 1]])
               {
                   $this->arLinkList[$key] .=  $arRulesTranslit[$tempVar[$i] . $tempVar[$i + 1]];
                   $i++;
               }
                elseif(isset($arRulesTranslit[$tempVar[$i]]))
                {
                    $this->arLinkList[$key] .=  $arRulesTranslit[$tempVar[$i]];
                }
                else
                {
                    $this->arLinkList[$key] .=  $tempVar[$i];
                }

            }

        }




    }

    //Метод преобразует запись из столбца с начальной датой в первоначальный вид
    public function recoveryDateFormat($string)
    {
        $arString = str_split($string);
        if(count($arString) == 7)
            array_unshift($arString, "0");

        $newString = implode("", $arString);
        $newString = substr($newString, 0, 2) . '-' . substr($newString, 2, 2) . '-' .  substr($newString, 4);

        return $newString;
    }


}