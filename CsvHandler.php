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


    //����� ���������� ������ ����� ��� ������
    public function printLink()
    {

        $arRulesTranslit = array(
                    "�" => "a", "��" => "iy", "��" => "ie", "�" => "b", "�" => "v",
                    "�" => "g", "�" => "d",   "�" => "e", "�" => "yo", "�" => "zh",
                    "�" => "z", "�" => "i",   "�" => "y", "�" => "k",  "�" => "l",
                    "�" => "m", "�" => "n",  "�" => "o", "�" => "p",  "�" => "r",
                    "�" => "s", "�" => "t",  "�" => "u", "�" => "f",  "�" => "kh",
                    "�" => "ts","�" => "ch", "�" => "sh", "�" => "shch", "�" => "",
                    "�" => "y", "�" => "", "�" => "e", "�" => "yu", "�" => "ya",
                    "��" => "yo", "�" => "yi", "�" => "i", "�" => "ye", "�" => "g",
                     "." => "-", "," => "", "!" => "-", "?" => "-", " " => "-",
                      ";" => "/", ":" => "-");

        foreach($this->resultCsv as $key => $val)
        {

            $tempVar = implode(";", $val);
            //����������� ������� � ������ �������
            $tempVar = mb_strtolower($tempVar, 'cp-1251');

            for($i = 0; $i < iconv_strlen($tempVar); $i++)
            {
                //���� ������� � ��������� �� ��� ������� ������������ ������������ � �������, �� ��� ���������� �� ��������������� ��������.
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

    //����� ����������� ������ �� ������� � ��������� ����� � �������������� ���
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