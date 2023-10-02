const fs = require("fs")
const express = require("express")
const router = express.Router()
const mysql = require("mysql2/promise")
const bcrypt = require("bcrypt")
const { error } = require("console")
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

    class RheaiBase{

        vers = "0.0.0"
        sortie = ""
        blockchain_id =""
        input = []
        output= []
        variable=[]

        constructor(code){
            this.code = code
        }

        execute(){
            n = 0
            while (n < this.code.lenght){
                switch(this.code[n][0]){
                    case "Rheia":
                        s = this.version(this.code[n])
                        n += s
                        break
                    case "ON":
                        s = this.changeBlockchain(this.code[n])
                        n += s
                        break
                    case "END":
                        s = this.end(this.code[n])
                        n += s
                        break
                    case "VAR":
                        s = this.var(this.code[n])
                        n += s
                        break
                }
            }
        }

        version(ligne){
            if (ligne[1] = this.vers){
                return 1
            }else
            {
                this.sortie = "Error, Not Rheia : " + vers
                return this.code.lenght
            }
        }

        async changeBlockchain(ligne) {
            blockchain = await query("SELECT id FROM game_blockchain WHERE blockchain_token_id = ?", [ligne[1]])
            if(blockchain.lenght > 0){
                this.blockchain_id = blockchain[0].id
                return 1
            }else 
            {
                this.sortie = "Error, Blockchain Don't Exist : " + blockchain
                return this.code.lenght
            }
            
        }

        end(ligne){
            this.output = ligne.slice(1)
            this.sortie = "Success" 
            return this.code.lenght
        }   
        var(ligne){1

            this.variable[ligne[1]] = ligne[2]
        }
    }

    class SmartContract {


        constructor (code) {
            if(code[0][0]== "Rheia")
            {
                switch(code[0][1]){
                    case "0.0.0":
                        this.Rheia = new RheaiBase(code)
                        break

                }
            }else{
                console.log("Error, No Rheia !!")
                return "Error"
            }
        }
    }

    const SCName = req.body.name
    const SCreq = await query("SELECT code FROM smart_contract WHERE name = ? ;", [SCName])
    const JSONSmartContract = SCreq[0].code
    const SC = (JSON.parse(JSONSmartContract)).list  //{list:[["Rheia","6.1.8"],["Facility","5.7.4"],["ON","Ethereum"],[]]}


})