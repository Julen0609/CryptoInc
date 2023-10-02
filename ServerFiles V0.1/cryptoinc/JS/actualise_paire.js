const fs = require("fs")
const express = require("express")
const router = express.Router()
const mysql = require("mysql2/promise")
const bcrypt = require("bcrypt")
module.exports = router;

let database_infos 

fs.readFile("./JS/database.json", function(err, data) {
    if (err) throw err;
    database_infos = JSON.parse(data);
});


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

    const user = req.body.user
    const password = req.body.password
    const contrat = req.body.contrat
    const filtre = /^[A-Za-z0-9_@]+$/;


    if(!user.match(filtre)) {
        return res.status(400).send("8")
    }

    const dbusername = await query("SELECT username FROM users WHERE id = ?;", [user])

    if (!dbusername[0]){
        console.log(user)
        console.log(dbusername)
        return res.status(400).send("5")
    }

    const dbpassword = await query("SELECT hash FROM users WHERE id = ?;", [user])

    
    dbHash = dbpassword[0]["hash"]

    if (!await bcrypt.compare(password, dbHash)){
        return res.status(400).send("6")
    }

    const dbData = await query("SELECT argent FROM users WHERE id = ? ", [user])

    const paire = await query("SELECT contrat_ex.prix, contrat_ex.supply, game_token.name, contrat_ex.type, contrat_ex.leverage_max FROM contrat_ex INNER JOIN game_token ON contrat_ex.token_id = game_token.id WHERE contrat_ex.id = ?;", [contrat])

    const hold = await query("SELECT trades_ex.quantite, trades_ex.etat, trades_ex.id, trades_ex.prix_achat, trades_ex.liquidation FROM trades_ex WHERE trades_ex.id_contrat = ? AND trades_ex.id_user = ? AND trades_ex.etat != 'end';", [contrat,user])

    console.log(paire)
    console.log(hold)

    const jsonPaire = JSON.stringify(paire[0])

    const hold_list = {
        list: hold
    }

    const jsonHold= JSON.stringify(hold_list)

    res.send("0\t"+jsonPaire+"\t"+ jsonHold+"\t"+dbData[0].argent)


})