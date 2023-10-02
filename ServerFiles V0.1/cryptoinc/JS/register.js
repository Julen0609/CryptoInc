const fs = require("fs")
const express = require("express")
const router = express.Router()
const mysql = require("mysql2/promise")
const bcrypt = require("bcrypt")
//const bp = require("body-parser")
module.exports = router;



let database_infos 

fs.readFile("./JS/database.json", function(err, data) {
    if (err) throw err;
    database_infos = JSON.parse(data);
});



function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min)) + min;
}

router.post('/', async (req,res) => {
    const con = mysql.createPool({
        host: database_infos.host,
        user: database_infos.user,
        password: database_infos.password,
        database: database_infos.database
    })

    const query = async (query, values) => {                    //creation d'une fonction pour faciliter les requetes sql

        
        const [rows] = await con.execute(query, values)
        return rows
    }

    const login = req.body.name
    const password = req.body.password
    const filtre = /^[A-Za-z0-9_@]+$/;

    console.log(login)

    if(!login.match(filtre)) {
        return res.status(400).send("8")
    }

    console.log("test passé")
    
    const dbuser = await query("SELECT username FROM users WHERE username = ?;", [login])

    console.log(dbuser)

    if (dbuser[0]){
        return res.status(400).send("3")
    }


    salt = getRandomInt(10,12)
    console.log(salt)

    const pass_hash = await bcrypt.hash(password, salt)
    
    console.log(pass_hash)
    const results = await query ("INSERT INTO users (id, username, hash, salt, argent) VALUES (0,?,?,?,?)", [login, pass_hash,salt,1000])

    return res.status(200).send("0")

})






//Filtrage SQL Scripts :

//Filtrage uniquement Lettre et Maj

//const filtreLettre = /^[A-Za-z]+$/;

//if(!userEntree.match(filtreLettre)){
//  return res.status(400).json({err: "No Special character and no numbers"})
//}

//Filtrage uniquement nombre

//  if(isNaN(Number(userEntree))){
//      return res.status(400).json({err: "Number Only"})
//}

// Filtrage par Whitelist 

// const tagValid = ["js", "test", "bruh"]

// if(!tagValid.includes(userEntree)){
//      return res.status(400).json({err: "Not a valid Tag"})
//}




//RECHERCHE SUR LES EXPRESSION RATIONNELLES :

// l'expression :       /ab*c/      validerait un str comme celui ci : "aaaaplsabbbbbc" car il contient "abbbbbc" , soit un "a", un nombre allant de 0 a l'infini de b et un c.
// Par exemple /ab*c/ ne validerait pas une chaine sous cette forme : "aaaabbbbbaaaba" car le c n'est pas présent, elle ne validerait pas non plus celle la : "bbbbca" car ce n'est pas le bon ordre
// Par contre le str comme celui ci serait valide : "aaaac" , grace a "ac" car il y a bien un "a" suivi d'un multiple de "b" (*0) puis un "c"


// de meme pour :       /ab+c/      a la difference que ici il n'est pas possible de prendre en compte la chaine "ac" car b doit au moin etre présent une fois.

// /ab?c/ correspondra si b est présent une ou 0 fois : autrement dit "ac" fonctionnera, "abc" aussi mais "abbc" ne fonctionnera pas du tout


// /ab{2}c/     ne fonctionnera que si il y a 2 b, ce serait revenu au même que d'ecrire /abbc/ 

// /ab{2,}c/    celui ci fonctionnera de la même maniere que le "+" a la difference qu'il commence de 2 (ou de n'importe quel nombre mis entre crochets) jusqu'a l'infini.

// /ab{2,4}c/   bon... En toute logique c'est pas compliqué, ca comprend "abbc", "abbbc" et "abbbbc" bref entre 2 et 4 repetition de b


//CLASSE DE CARACTERES

//      /\d/    (=/[0-9]/)          soit un chiffre entre 0 et 9

//      /\D/    (=/[^0-9]/)         soit tout sauf un chiffre entre 0 et 9

//      \w                          soit tout caractéres Latin de a-z a A-Z en passant pas 0-9
//      \W      l'inverse

//      \s                          correspond a un blanc ( espace, saut de ligne, tab...)
//      \S                          correspond a une chaine sans blanc



//  /Jack(?=Sparrow)/ correspond à 'Jack' seulement s'il est suivi de 'Sparrow'. /Jack(?=Sparrow|Bauer)/ correspond à 'Jack' seulement s'il est suivi de 'Sparrow' ou de 'Bauer'. Cependant, ni 'Sparrow' ni 'Bauer' ne feront partie de la correspondance.

//  /\d+(?!\.)/ correspond à un nombre qui n'est pas suivi par un point, cette expression utilisée avec la chaîne 3.141 correspondra pour '141' mais pas pour '3.141'.      (\. correspond a un ".")

//  /(?<=Jack)Sprat/ correspond à "Sprat" seulement s'il est précédé de "Jack".     /(?<=Jack|Tom)Sprat/ correspond à "Sprat" seulement s'il est précédé de "Jack" ou "Tom".    Toutefois, "Jack" et "Tom" ne feront pas partie de la correspondance.

//  /(?<!-)\d+/ correspondra à un nombre seulement si celui-ci n'est pas précédé d'un signe moins.  /(?<!-)\d+/.exec('3') cible "3".    /(?<!-)\d+/.exec('-3') ne trouve aucune correspondance car le nombre est précédé d'un signe