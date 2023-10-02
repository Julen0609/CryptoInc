const fs = require("fs")
const express = require("express")
const router = express.Router()
const mysql = require("mysql2/promise");
const { json } = require("body-parser");
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

    const exchange = req.body.exchange

    const contrat = await query("SELECT game_token.sigle, contrat_ex.id FROM game_token INNER JOIN contrat_ex ON game_token.id = contrat_ex.token_id INNER JOIN game_exchange ON contrat_ex.exchange_id = game_exchange.id WHERE game_exchange.id = ? ORDER BY game_token.price DESC;", [exchange])

    console.log(contrat)

    const contrat_list = {
        list: contrat
    }

    jsonData = JSON.stringify(contrat_list)
    return res.send("0\t"+jsonData)

})