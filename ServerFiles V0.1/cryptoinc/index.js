

const express = require("express")
const port = process.env.PORT || 5000
const mysql = require("mysql2/promise")
const bcrypt = require("bcrypt")
const bp = require("body-parser")

const app = express();
app.use(bp.urlencoded({ extended: true }));



const registerUrl = require("./JS/register.js") // Permet de rediriger la route vers le fichier user dans le dossier routes.
app.use("/register", registerUrl)     //pour le coup, dans ce cas jsp si c'est utile...

const loginUrl = require("./JS/login.js")
app.use("/login", loginUrl)

const get_exchUrl = require("./JS/get_exch.js")
app.use("/get_exch", get_exchUrl)

const get_contratUrl = require("./JS/get_contrat.js")
app.use("/get_contrat", get_contratUrl)

const actualise_paireUrl = require("./JS/actualise_paire.js")
app.use("/actualise_paire", actualise_paireUrl)

const take_tradeUrl = require("./JS/take_trade.js")
app.use("/take_trade", take_tradeUrl)

const clotureUrl = require("./JS/cloture.js")
app.use("/cloture", clotureUrl)




app.get('/test', async(req,res) => {
    res.json({index: true})
})

app.listen(port, () => {
    console.log("serveur est en ligne !")
})


