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

    const user = parseFloat(req.body.iduser)
    const password = req.body.password
    const idtrade = parseFloat(req.body.idtrade)

    let quantite_user = parseFloat(req.body.quantite)


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

    const argent = parseFloat(dbData[0].argent)

    const trades = await query("SELECT trades_ex.quantite, trades_ex.prix_achat, trades_ex.id_contrat, trades_ex.id_user, trades_ex.etat, contrat_ex.prix, contrat_ex.fees,contrat_ex.max_supply, contrat_ex.supply FROM trades_ex INNER JOIN contrat_ex ON contrat_ex.id = trades_ex.id_contrat WHERE trades_ex.id = ? ;", [idtrade])
    const trade = trades[0]

    if (trade.id_user != user)
    {
        return res.status(400).send("16") // error code #16 - User is not trade owner
    }
    if (trade.etat == "end")
    {
        return res.status(400).send("17") // error code #17 - Trade didn't exist already
    }
    if (trade.quantite < quantite_user)
    {
        return res.status(400).send("13") // error code #13 - not enough crypto
    }

    const rest = trade.quantite-quantite_user
    const levier = parseFloat(trade.etat.substring(4))
    
    

    if (trade.etat.substring(0,4) == "long")
    {
        if (parseFloat(trade.supply) + quantite_user > trade.max_supply)
        {
            return res.status(400).send("11") // error code #11 - not enough supply
        }

        const roi = quantite_user* parseFloat(trade.prix) - parseFloat(trade.fees)*levier - parseFloat(trade.prix_achat)*quantite_user*(1-1/levier)

        if (argent+ roi < 0){
            return res.status(400).send("9") // error code #9 - not enough money
        }
        
        if (rest == 0)
        {
            const modif = await query("UPDATE trades_ex SET etat = 'end' WHERE trades_ex.id = ? ;",[idtrade])
            const update = await query("UPDATE users SET argent = ? WHERE id = ? ;",[argent+roi, user])
            const update_contrat = await query("UPDATE contrat_ex SET supply = ? WHERE id = ? ;",[ parseFloat(trade.supply)+quantite_user, trade.id_contrat])
        }else {
            const modif = await query("UPDATE trades_ex SET quantite = ? WHERE trades_ex.id = ? ;",[rest, idtrade])
            const update = await query("UPDATE users SET argent = ? WHERE id = ? ;",[argent+roi, user])
            const update_contrat = await query("UPDATE contrat_ex SET supply = ? WHERE id = ? ;",[ parseFloat(trade.supply)+quantite_user, trade.id_contrat])
        }
    }else if(trade.etat.substring(0,4) == "shrt")
    {
        if (parseFloat(trade.supply) < quantite_user)
        {
            return res.status(400).send("11") // error code #11 - not enough supply
        }

        const roi = parseFloat(trade.prix_achat)*quantite_user - parseFloat(trade.fees)*levier - quantite_user* parseFloat(trade.prix) *(1-1/levier)

        if (argent+ roi < 0){
            return res.status(400).send("9") // error code #9 - not enough money
        }
        
        if (rest == 0)
        {
            const modif = await query("UPDATE trades_ex SET etat = 'end' WHERE trades_ex.id = ? ;",[idtrade])
            const update = await query("UPDATE users SET argent = ? WHERE id = ? ;",[argent+roi, user])
            const update_contrat = await query("UPDATE contrat_ex SET supply = ? WHERE id = ? ;",[trade.supply-quantite_user, trade.id_contrat])
        }else {
            const modif = await query("UPDATE trades_ex SET quantite = ? WHERE trades_ex.id = ? ;",[rest, idtrade])
            const update = await query("UPDATE users SET argent = ? WHERE id = ? ;",[argent+roi, user])
            const update_contrat = await query("UPDATE contrat_ex SET supply = ? WHERE id = ? ;",[trade.supply-quantite_user, trade.id_contrat])
        }
    }else{
        return res.status(400).send("18") // error code #18 - Not short or long 
    }

    return res.send("0")
    
    

})  