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

    echo($code);
    echo "\t";

    $code = preg_replace("#(\r\n|\n\r|\n|\r)#","",$code);
    

    $line_array = explode(";", $code);

    $blockchain_used = "";
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

    echo"0";







    function FONCTIONS ($word, $words)
    {
        print_r($words);
        echo "\t";
        global $blockchain_used, $token_used, $wallet1, $wallet2, $number, $output, $input, $var, $smart_contract;
        echo $blockchain_used;
        echo "\t";
        echo $number;
        echo "\t";
        print_r($var);
        echo "\t";
        
        if ($word == "ON") 
        {
            if ($words[1] != "")
            {
                $blockchain_used = MACRO($words[1]);
            }
            else
            {
                echo "Aucune Blockchain renseignée";
                echo "\t";
            }
        } 
        elseif ($word == "SEND") //Lancement de SEND
        {
            if (count($words) == 8)
            {
                $token_used = MACRO($words[1]);

                if ($words[2] == "NUMBER") //Verifie NUMBER
                {
                    if ($words[3] != "")
                    {
                        $number = OPERATOR($words[3]);

                        if ($words[4] == "FROM")// Verifie FROM
                        {
                            if ($words[5] != "")
                            {
                                $wallet1 = MACRO($words[5]);

                                if ($words[6] == "TO") // Verifie TO
                                {
                                    if ($words[7] != "")
                                    {
                                        $wallet2 = MACRO($words[7]); //Suite de la commande SEND aprés ca

                                        SEND($blockchain_used,$token_used,$number,$wallet1,$wallet2);
                                    }
                                    else
                                    {
                                        echo "Aucun Destinataire renseigné";
                                        echo "\t";
                                    }
                                }
                                else
                                {
                                    echo "Commande TO Manquante";
                                    echo "\t";
                                } //Fin TO
                            }
                            else
                            {
                                echo "Aucun Envoyeur renseigné";
                                echo "\t";
                            }
                        }
                        else
                        {
                            echo "Commande FROM Manquante";
                            echo "\t";
                        } //Fin FROM
                    }
                    else
                    {
                        echo "Aucun Nombre renseigné";
                        echo "\t";
                    }
                }
                else
                {
                    echo "Commande NUMBER Manquante";
                    echo "\t";
                } //Fin NUMBER
            }
            else
            {
                echo "Entrées manquantes pour réaliser cette commande, essayez : SEND token NUMBER 10 FROM wallet1 TO wallet2";
                echo "\t";
            } 


        } //Fin SEND
        elseif ($word == "RETURN")
        {
            if (str_contains($words[1], ":"))
            {
                $return = explode(":" , $words[1]);
                if ($return[0] != "" && $return[1] != "" )
                {
                    $output[OPERATOR($return[0])] = OPERATOR($return[1]);
                }
                else 
                {
                    echo "Fromat de Sortie Invalide, revoyez le format clé:valeur";
                    echo "\t";
                }
                
            }
            else
            {
                echo "Sortie invalide, verifiez bien le format clé:valeur";
                echo "\t";
            }
        }
        elseif ($word == "START")
        {
            if ($words[1] != "")
            {
                $smart_contract = MACRO($words[1]);
                foreach ($words as $wrd)
                {
                    if (str_contains($wrd, ":"))
                    {
                        $param = explode(":" , $wrd);
                        if ($param[0] != "" && $param[1] != "" )
                        {
                            $input[OPERATOR($param[0])] = OPERATOR($param[1]);
                        }
                        else 
                        {
                            echo "Fromat d'Entrée Invalide, revoyez le format clé:valeur";
                            echo "\t";
                        }
                        
                    }
                    else 
                    {
                        echo "Entrée invalide, verifiez bien le format clé:valeur";
                        echo "\t";
                    }
                }
                //Start le smart contract

            }
            else
            {
                echo "Veuillez renseigner le nom d'un Smart conract";
                echo "\t";
            }
        }
        elseif ($word == "END")
        {
            exit();
        }
        elseif (substr($word, 0, 1) == "$")
        {
            $variable = explode("=", $words[0]);

            print_r($variable);
            
            if ($variable[0] != "" && $variable[1] != "" )
            {
                
                $var[$variable[0]] = OPERATOR($variable[1]);
                echo $var[$variable[0]];
                echo $variable[0];
                echo "\t";
                
            }
            else 
            {
                echo "Fromat de Variable Invalide, revoyez le format nom=valeur (Attention, NE PAS METTRE D'ESPACE ENTRE LES CLES/VALEURS ET = , RISQUE DE FAIRE PLANTER TOUT LE PROGRAMME !))";
                echo "\t";
            }
        }
    }

    function MACRO ($word)
    {
        global $input, $output, $var;
        if (substr($word, 0, strlen($word)- strpos($word, "[")) == "INPUT") // ...°INPUT°[test];
        {
            $param = substr($word,strlen($word)- strpos($word, "[") +1, strlen($word)- strpos($word, "[") - strlen($word)- strpos($word, "]") );
            if (in_array( $param, array_keys($input))) //...INPUT[°test°];
            {
                return $input[$param];
            }
            else
            {
                echo "Aucun Input renseigné ou Input faux";
                echo "\t";
            }
        }
        elseif (substr($word, 0, strlen($word)- strpos($word, "[")) == "OUTPUT")// ...°OUTPUT°[test];
        {
            $param = substr($word,strlen($word)- strpos($word, "[") +1, strlen($word)- strpos($word, "[") - strlen($word)- strpos($word, "]") );
            if (in_array($param, array_keys($output))) // ...OUTPUT[°test°];
            {
                return $output[$param];
            }
            else
            {
                echo "Aucun Output renseigné ou Output faux";
                echo "\t";
            }
        }
        elseif (substr($word, 0, 1) == "$")// ...°$°var2;
        {
            echo $word;
            echo "\t";
            if (in_array($word,array_keys($var))) // ...$°var2°;
            {
                return $var[$word];
            }
            else
            {
                echo "Variable renseignée inexistante(Attention, NE PAS METTRE D'ESPACE ENTRE LES CLES/VALEURS ET = , RISQUE DE FAIRE PLANTER TOUT LE PROGRAMME !)";
                echo "\t";
            }
        }
        else 
        {
            return $word;
        }
    }

    function OPERATOR ($word) // ATTENTION A CETTE FONCTION : elle recuperera les +-*/ qui sont aussi dans les [] ou les () en+ elle ne fait pas les priorités de calcul.
    {                          // FAIRE LES CALCULS SUR PLUSIEURS LIGNES SI PLUSIEURS OPERATIONS OU DIMENSIONS ( 2+4*8 ne fonctionera pas par exemple, plutot faire : r=4*8; 2+r;)
                                            //INPUT[1+INPUT[2-4]]-INPUT[2*5]/OUTPUT[$test]
        
        
        if (str_contains($word, "+")) // ...INPUT[test]°+°OUTPUT[truc]
        {
            if(!str_contains($word,"-") && !str_contains($word,"*") && !str_contains($word,"/"))
            {
                $calcul = explode("+",$word); //commencer la partie calcul (+-*/%)
                if ($calcul[0] != "" && $calcul[1] != "")
                {
                    return MACRO($calcul[0])+MACRO($calcul[1]);
                }
            }
            else
            {
                echo "Plusieurs calculs sur la même ligne";
                echo "\t";
            }
            
        }
        else return MACRO($word);
    }

    function SEND ($blockchain, $token, $number, $wallet1, $wallet2)
    {
        global $con;

        //SELECT user_token.number,user_token.id, bc_wallet.cle_publique, bc_wallet.cle_privee FROM user_token INNER JOIN game_token ON game_token.id = user_token.id_token INNER JOIN bc_wallet ON bc_wallet.id = user_token.id_wallet INNER JOIN game_blockchain ON game_blockchain.id = game_token.blockchain_id WHERE game_blockchain.blockchain_token_id = "ERC-20" AND game_token.sigle = 'Eth' AND (bc_wallet.cle_privee = 'test' OR bc_wallet.cle_publique = 'test');

        $get_wallet1_token = "SELECT user_token.number,user_token.id, bc_wallet.cle_publique, bc_wallet.cle_privee FROM user_token INNER JOIN game_token ON game_token.id = user_token.id_token INNER JOIN bc_wallet ON bc_wallet.id = user_token.id_wallet INNER JOIN game_blockchain ON game_blockchain.id = game_token.blockchain_id WHERE game_blockchain.blockchain_token_id = '".$blockchain."' AND game_token.sigle = '".$token."' AND (bc_wallet.cle_privee = '".$wallet1."' OR bc_wallet.cle_publique = '".$wallet1."');";
        $getted_wallet1_token = mysqli_query($con, $get_wallet1_token) or die("2"); // error code #2 - data recovery failed

        if (mysqli_num_rows($getted_wallet1_token) != 1)
        {
            echo "Le wallet ne possede pas ce token";
        }

        $wallet1_token = mysqli_fetch_assoc($getted_wallet1_token);


        if ($wallet1_token < $number)
        {
            echo "Le wallet ne possede pas assez de ce token";
            exit(); 
        }

        $get_wallet2_token = "SELECT user_token.number,user_token.id, bc_wallet.cle_publique, bc_wallet.cle_privee FROM user_token INNER JOIN game_token ON game_token.id = user_token.id_token INNER JOIN bc_wallet ON bc_wallet.id = user_token.id_wallet INNER JOIN game_blockchain ON game_blockchain.id = game_token.blockchain_id WHERE game_blockchain.blockchain_token_id = '".$blockchain."' AND game_token.sigle = '".$token."' AND (bc_wallet.cle_privee = '".$wallet2."' OR bc_wallet.cle_publique = '".$wallet2."');";
        $getted_wallet2_token = mysqli_query($con, $get_wallet2_token) or die("2"); // error code #2 - data recovery failed

        if (mysqli_num_rows($getted_wallet1_token) != 1)
        {
            $new_wallet2_token = $number;
            $insertion= "INSERT INTO user_token (id_token, id_wallet, number) VALUES ('".$token."', '".$wallet2."', 0 );";
            mysqli_query($con, $insertion) or die("4"); //error code #4 - insert query failed
            $getted_wallet2_token = mysqli_query($con, $get_wallet2_token) or die("2"); // error code #2 - data recovery failed
            $wallet2_token = mysqli_fetch_assoc($getted_wallet2_token);
        }
        else 
        {
            $wallet2_token = mysqli_fetch_assoc($getted_wallet2_token);
            $new_wallet2_token = $wallet2_token["number"] + $number ;
        }

        $new_wallet1_token = $wallet1_token["number"] - $number;

        if ($wallet1_token["cle_privee"] == $wallet1 && $wallet2_token["cle_privee"] == $wallet2)
        {
            $update_query = "UPDATE user_token SET user_token.number = '".$new_wallet1_token."' WHERE user_token.id = '".$wallet1_token["id"]."';";
            mysqli_query($con, $update_query) or die ("10"); // error code #10 - data update failed
            $update_query = "UPDATE user_token SET user_token.number = '".$new_wallet2_token."' WHERE user_token.id = '".$wallet2_token["id"]."';";
            mysqli_query($con, $update_query) or die ("10"); // error code #10 - data update failed


            
        } //Preparer les cas suivants ou on a pas la clé privée mais la cl" publique ( preparer une liste des commandes en attentes...)
        
    }

    




?>