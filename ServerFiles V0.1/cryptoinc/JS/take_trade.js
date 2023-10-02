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
    const idcontrat = parseFloat(req.body.idcontrat)

    let quantite_user = parseFloat(req.body.quantite)
    const transaction = req.body.transaction
    let levier

    if ( transaction == "long" || transaction == "short")
    {
        levier = parseFloat(req.body.levier)
    }else
    {
        levier = 1
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

    const argent = parseFloat(dbData[0].argent)

    const contrats = await query("SELECT contrat_ex.prix, contrat_ex.fees, contrat_ex.supply, contrat_ex.max_supply, contrat_ex.type, contrat_ex.leverage_max FROM contrat_ex WHERE contrat_ex.id = ? ;", [idcontrat])

    const contrat = contrats[0]

    const contrat_prix = parseFloat(contrat.prix)
    const contrat_fees = parseFloat(contrat.fees)
    const contrat_supply = parseFloat(contrat.supply)
    const contrat_max_supply = parseFloat(contrat.max_supply)
    const contrat_leverage_max = parseFloat(contrat.leverage_max)

    //const prix_tot = contrat.prix * quantite / levier + (contrat.fees* $levier)

    if (contrat.type == "spot")
    {
        if (transaction == "buy")
        {
            console.log("buy")
            const prix_tot = contrat_prix * quantite_user + contrat_fees

            if (contrat_supply < quantite_user )
            {
                return res.status(400).send("11")
            }
            const new_supply = contrat_supply - quantite_user
            if (argent - prix_tot < 0)
            {
                return res.status(400).send("9")
            }
            const insertion = await query("INSERT INTO trades_ex (id_contrat, id_user, prix_achat, etat, quantite, liquidation) VALUES (?,?,?,'spot',?, 0);", [idcontrat, user, contrat_prix, quantite_user])
            const update_data = await query("UPDATE users SET argent = ? WHERE id = ?;", [argent-prix_tot, user])
            const update_contrat = await query("UPDATE contrat_ex SET supply = ? WHERE id = ?;", [new_supply, idcontrat])

            
        }

        if (transaction == "sell")
        {
            const prix_tot = contrat_prix * quantite_user - contrat_fees

            

            if (contrat_supply + quantite_user > contrat_max_supply )
            {
                return res.status(400).send("12")
            }

            console.log("start_sell")

            const getHold = await query ("SELECT trades_ex.quantite FROM trades_ex WHERE trades_ex.id_user = ? AND trades_ex.id_contrat = ? AND trades_ex.etat != 'end' ;", [user, idcontrat])

            let temp_hold = 0;

            getHold.forEach(hold => {
                temp_hold = temp_hold + parseFloat(hold.quantite)
            });

            console.log("passed for Each         "+temp_hold)

            if (temp_hold < quantite_user) 
            {
                return res.status(400).send("13")
            }

            const new_supply = contrat_supply + quantite_user

            const Trades = await query("SELECT trades_ex.quantite, trades_ex.id, trades_ex.prix_achat FROM trades_ex WHERE trades_ex.id_user = ? AND trades_ex.id_contrat = ? AND trades_ex.etat != 'end';", [user, idcontrat])

            console.log("passed query")

            let n = 0
            while (quantite_user != 0)
            {
                if ( parseFloat(Trades[n].quantite) >= quantite_user)
                {
                    console.log(new_supply+"      "+idcontrat)
                    const modif = await query ("UPDATE trades_ex SET quantite = ? WHERE trades_ex.id = ? ;", [parseFloat(Trades[n].quantite)-quantite_user, Trades[n].id])
                    const userUpdate = await query ("UPDATE users SET argent = ? WHERE id = ? ;", [argent+prix_tot, user])
                    const contratUpdate = await query ("UPDATE contrat_ex SET supply = ? WHERE id = ? ;", [new_supply, idcontrat])
                    quantite_user = 0
                    
                    
                }
                else if(Trades[n].quantite < quantite_user)
                {
                    const modif = await query ("UPDATE trades_ex SET etat = 'end' WHERE trades_ex.id = ? ;", [Trades[n].id])
                    quantite_user = quantite_user - Trades[n].quantite
                    console.log("bas")
                    
                }
                console.log(Trades[n].quantite)
                n = n+1
            }
        }
    }else if (contrat.type == "derivee")
    {
        const prix_tot = contrat_prix * quantite_user / levier + (contrat_fees/levier)

        if (levier > contrat_leverage_max && levier < 1)
        {
            return res.status(400).send("15")
        }

        

        if (transaction == "long")
        {
            

            if (contrat_supply < quantite_user )
            {
                return res.status(400).send("11")
            }

            const new_supply = contrat_supply - quantite_user

            if (argent - prix_tot < 0)
            {
                return res.status(400).send("9")
            }

            const insertion = await query("INSERT INTO trades_ex (id_contrat, id_user, prix_achat, etat, quantite, liquidation) VALUES (? ,? ,? , ?,?, ? );", [idcontrat,user,contrat_prix,"long"+levier,quantite_user, contrat_prix-(contrat_prix/levier-contrat_fees*levier)])
            const userUpdate = await query("UPDATE users SET argent = ? WHERE id = ? ;", [argent-prix_tot, user])
            const contratUpdate = await query("UPDATE contrat_ex SET supply = ? WHERE id = ? ;", [new_supply, idcontrat])


        }
        if (transaction == "short")
        {
            

            if (contrat.supply + quantite_user > contrat.max_supply )
            {
                return res.status(400).send("12")
            }

            const new_supply = contrat.supply + quantite_user

            if (argent - prix_tot < 0)
            {
                return res.status(400).send("9")
            }

            const insertion = await query("INSERT INTO trades_ex (id_contrat, id_user, prix_achat, etat, quantite, liquidation) VALUES (? ,? ,? ,?,?, ? );", [idcontrat,user,contrat_prix,"shrt"+levier,quantite_user, contrat_prix+(contrat_prix/levier-contrat_fees*levier)])
            const userUpdate = await query("UPDATE users SET argent = ? WHERE id = ? ;", [argent-prix_tot, user])
            const contratUpdate = await query("UPDATE contrat_ex SET supply = ? WHERE id = ? ;", [new_supply, idcontrat])


        }

    }else{
        return res.status(400).send("14")
    }


    res.send("0")

})