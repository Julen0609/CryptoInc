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


router.post('/', async (req,res) => {
    const con = mysql.createPool({
        host: database_infos.host,
        user: database_infos.user,
        password: database_infos.password,
        database: database_infos.database
    })

    console.log("login")

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

    const dbusername = await query("SELECT username FROM users WHERE username = ?;", [login])

    if (!dbusername[0]){
        return res.status(400).send("5")
    }

    const dbpassword = await query("SELECT hash FROM users WHERE username = ?;", [login])

    
    dbHash = dbpassword[0]["hash"]

    if (!await bcrypt.compare(password, dbHash)){
        return res.status(400).send("6")
    }

    const dbData = await query("SELECT id, argent FROM users WHERE username = ? ", [login])
    console.log(dbData[0])
    jsonData = JSON.stringify(dbData[0])
    return res.send("0\t"+jsonData)

})