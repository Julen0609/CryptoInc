<?php
    //SELECT code FROM smart_contract WHERE name = "test";


    $con = mysqli_connect('localhost','root', 'root', 'cryptoinc');
	//check that connection happend
	if (mysqli_connect_errno())
	{
		echo "1"; // error code #1 - Connection failed
		exit();
	}

    $name = $_POST["name"];

    $getSC = "SELECT code FROM smart_contract WHERE name = '".$name."';";

    $SCs = mysqli_query($con, $getSC) or die("2"); // error code #2 - data recovery failed

    $SC = mysqli_fetch_assoc($SCs);

    $code =  $SC["code"];

    $line_array = explode(";", $code);

    $blochchain_used = "";
    $token_used = "";
    $wallet1 = "";
    $wallet2 = "";
    $number = "";
    $var = array();
    $input = array();
    $output = array();
    $smart_contract = "";

    foreach ($line_array as $line)
    {
        $words = explode(" " , $line);
        FONCTIONS($words[0], $words);


    }


    function FONCTIONS ($word, $words)
    {
        global $blochchain_used, $token_used, $wallet1, $wallet2, $number, $output, $input, $var, $smart_contract;
        if ($word == "ON") 
        {
            if ($words[1] != "")
            {
                $blochchain_used = $words[1];
            }
            else
            {
                echo "Aucune Blockchain renseignée";
            }
        } 
        elseif ($word == "SEND") //Lancement de SEND
        {
            if ($words[1] != "")
            {
                $token_used = $words[1];

                if ($words[2] == "NUMBER") //Verifie NUMBER
                {
                    if ($words[3] != "")
                    {
                        $number = $words[3];

                        if ($words[4] == "FROM")// Verifie FROM
                        {
                            if ($words[5] != "")
                            {
                                $wallet1 = $words[5];

                                if ($words[6] == "TO") // Verifie TO
                                {
                                    if ($words[7] != "")
                                    {
                                        $wallet2 = $words[7]; //Suite de la commande SEND aprés ca
                                    }
                                    else
                                    {
                                        echo "Aucun Destinataire renseigné";
                                    }
                                }
                                else
                                {
                                    echo "Commande TO Manquante";
                                } //Fin TO
                            }
                            else
                            {
                                echo "Aucun Envoyeur renseigné";
                            }
                        }
                        else
                        {
                            echo "Commande FROM Manquante";
                        } //Fin FROM
                    }
                    else
                    {
                        echo "Aucun Nombre renseigné";
                    }
                }
                else
                {
                    echo "Commande NUMBER Manquante";
                } //Fin NUMBER
            }
            else
            {
                echo "Aucun Token renseigné";
            } 


        } //Fin SEND
        elseif ($word == "RETURN")
        {
            if (str_contains($words[1], ":"))
            {
                $return = explode(":" , $words[1]);
                if ($return[0] != "" && $return[1] != "" )
                {
                    $output[$return[0]] = $return[1];
                }
                else 
                {
                    echo "Fromat de Sortie Invalide, revoyez le format clé:valeur";
                }
                
            }
            else
            {
                echo "Sortie invalide, verifiez bien le format clé:valeur";
            }
        }
        elseif ($word == "START")
        {
            if ($words[1] != "")
            {
                $smart_contract = $words[1];
                foreach ($words as $wrd)
                {
                    if (str_contains($wrd, ":"))
                    {
                        $param = explode(":" , $wrd);
                        if ($param[0] != "" && $param[1] != "" )
                        {
                            $input[$param[0]] = $param[1];
                        }
                        else 
                        {
                            echo "Fromat d'Entrée Invalide, revoyez le format clé:valeur";
                        }
                        
                    }
                    else 
                    {
                        echo "Entrée invalide, verifiez bien le format clé:valeur";
                    }
                }
                //Start le smart contract

            }
            else
            {
                echo "Veuillez renseigner le nom d'un Smart conract";
            }
        }
        elseif ($word == "END")
        {
            exit();
        }
        elseif (substr($word, 0, 1) == "$")
        {
            $variable = explode("=", $words[0]);
            
            if ($variable[0] != "" && $variable[1] != "" )
            {
                $var[$variable[0]] = $variable[1];
            }
            else 
            {
                echo "Fromat de Variable Invalide, revoyez le format nom=valeur (Attention, NE PAS METTRE D'ESPACE ENTRE LES CLES/VALEURS ET = , RISQUE DE FAIRE PLANTER TOUT LE PROGRAMME !))";
            }
        }
    }

    function MACRO ($word)
    {
        global $input, $output, $var;
        if (substr($word, 0, strlen($word)- strpos($word, "[")) == "INPUT")
        {
            $param = substr($word,strlen($word)- strpos($word, "[") +1, strlen($word)- strpos($word, "[") - strlen($word)- strpos($word, "]") );
            if (in_array( $param, $input))
            {
                return $input[$param];
            }
            else
            {
                echo "Aucun Input renseigné ou Input faux";
            }
        }
        elseif (substr($word, 0, strlen($word)- strpos($word, "[")) == "OUTPUT")
        {
            $param = substr($word,strlen($word)- strpos($word, "[") +1, strlen($word)- strpos($word, "[") - strlen($word)- strpos($word, "]") );
            if (in_array($param, $output))
            {
                return $output[$param];
            }
            else
            {
                echo "Aucun Output renseigné ou Output faux";
            }
        }
        elseif (substr($word, 0, 1) == "$")
        {
            if (in_array($word, $var))
            {
                return $var[$word];
            }
            else
            {
                echo "Variable renseignée inexistante(Attention, NE PAS METTRE D'ESPACE ENTRE LES CLES/VALEURS ET = , RISQUE DE FAIRE PLANTER TOUT LE PROGRAMME !)";
            }
        }
        else 
        {
            return $word;
        }
    }

    function OPERATOR ($word)
    {
        if (str_contains($word, "+"))
        {
            $calcul = explode("+",$word); //commencer la partie calcul (+-*/%)
            if ($calcul[0] != 0)
            {

            }
        }
    }




?>