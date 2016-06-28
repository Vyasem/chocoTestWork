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


    //Ìåòîä âîçâðàùàåò ìàññèâ ñòðîê äëÿ ññûëîê
    publi function printLink()
    {

        $arRulesTranslit = array(
                    "à" => "a", "ûé" => "iy", "ûå" => "ie", "á" => "b", "â" => "v",
                    "ã" => "g", "ä" => "d",   "å" => "e", "¸" => "yo", "æ" => "zh",
                    "ç" => "z", "è" => "i",   "é" => "y", "ê" => "k",  "ë" => "l",
                    "ì" => "m", "í" => "n",  "î" => "o", "ï" => "p",  "ð" => "r",
                    "ñ" => "s", "ò" => "t",  "ó" => "u", "ô" => "f",  "õ" => "kh",
                    "ö" => "ts","÷" => "ch", "ø" => "sh", "ù" => "shch", "ü" => "",
                    "û" => "y", "ú" => "", "ý" => "e", "þ" => "yu", "ÿ" => "ya",
                    "éî" => "yo", "¿" => "yi", "³" => "i", "º" => "ye", "´" => "g",
                     "." => "-", "," => "", "!" => "-", "?" => "-", " " => "-",
                      ";" => "/", ":" => "-");

        foreach($this->resultCsv as $key => $val)
        {

            $tempVar = implode(";", $val);
            //ïðåîáðàçóåò ñèìâîëû â íèæíèé ðåãèñòð
            $tempVar = mb_strtolower($tempVar, 'cp-1251');

            for($i = 0; $i < iconv_strlen($tempVar); $i++)
            {
                //Åñëè òåêóùèé è ñëåäóþùèé çà íèì ñèìâîëû îäíîâðåìåííî ïðèñóòñòâóþò â ìàññèâå, òî îíè çàìåíÿþòñÿ íà ñîîòâåòñòâóþùèå çíà÷åíèÿ.
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

    //Ìåòîä ïðåîáðàçóåò çàïèñü èç ñòîëáöà ñ íà÷àëüíîé äàòîé â ïåðâîíà÷àëüíûé âèä
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
