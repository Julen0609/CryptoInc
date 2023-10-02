const fs = require("fs")
const express = require("express")
const router = express.Router()
const mysql = require("mysql2/promise")
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

    const exchange = await query("SELECT name, id FROM game_exchange ORDER BY game_exchange.fonds DESC;", [])

    const exchange_list =  {
        list: exchange
    }

    console.log(exchange_list)

    jsonData = JSON.stringify(exchange_list)
    return res.send("0\t"+jsonData)

})
