using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;
using System;

public class trade_list : MonoBehaviour
{



    public GameObject details;
    public UIManager UIManager;

    public Text dtl_leverage;
    public Text dtl_profit_percent;
    public Text dtl_profit_dollard;
    public Text dtl_quantite_crypto;
    public Text dtl_quantite_dollard;
    public Text dtl_prix_dachat;
    public Text dtl_type;
    public Text dtl_paire;
    public Text dtl_liquidation;

    public InputField entree;

    public double idtrade;
    public double quantite;



    public GameObject trade;
    public List<trade> list_trade = new List<trade>();
    // Start is called before the first frame update
    void Start()
    {
        
        
    }

    // Update is called once per frame
    void Update()
    {
        
    }

    public void Initialize(int number)
    {
        Clear();
        for (int i = 0; i < number; i++)
        {
            list_trade.Add(Instantiate(trade, this.transform).GetComponent<trade>());//instancie le trade et l'ajoute a la liste
        }
        foreach (trade trade in list_trade)
        {
            trade.trade_List = this;
        }
    }

    public void Clear()
    {
        foreach (trade trade in list_trade)
        {
            trade.Dstr();
        }

        list_trade.Clear();
    }

    public void Detail(double id_trade, trade trade_panel)
    {
        details.SetActive(true);
        dtl_leverage.text = "Levier : x" + trade_panel.leverage.ToString() ;
        dtl_prix_dachat.text = "Prix D'achat : " + trade_panel.prix_dachat.ToString() ;

        var profit_dollard = new double();
        var roi = new double();
        var temp_scale = "test";
        var temp_float = new float();


        if (trade_panel.type_trade == "Long")

        {
            roi = trade_panel.prix * trade_panel.quantite - trade_panel.prix_dachat * trade_panel.quantite * (1f - 1f / trade_panel.leverage);
            profit_dollard = roi - trade_panel.prix_dachat* trade_panel.quantite / trade_panel.leverage;
        }else if (trade_panel.type_trade == "Short")
        {
            roi = trade_panel.prix_dachat * trade_panel.quantite - trade_panel.prix * trade_panel.quantite * (1f - 1f / trade_panel.leverage);
            profit_dollard = roi - trade_panel.prix * trade_panel.quantite / trade_panel.leverage;
        }

        GameManager.instance.concatenate_money(profit_dollard, ref temp_float, ref temp_scale);
        dtl_profit_dollard.text = (temp_float).ToString() + temp_scale + " $";
        dtl_profit_percent.text = ((float) Math.Round((profit_dollard / trade_panel.initial_invest * 100), 3)).ToString() + " %"; 
        GameManager.instance.concatenate_money(trade_panel.quantite, ref temp_float, ref temp_scale);
        dtl_quantite_crypto.text = temp_float.ToString()+ temp_scale + " " + UIManager.crypto_sigle;
        GameManager.instance.concatenate_money((trade_panel.quantite * trade_panel.prix), ref temp_float, ref temp_scale);
        dtl_quantite_dollard.text = temp_float.ToString() + temp_scale + " $";
        dtl_paire.text = UIManager.crypto_sigle;
        dtl_type.text = trade_panel.type_trade;
        idtrade = trade_panel.id_trade;
        quantite = trade_panel.quantite;
        GameManager.instance.concatenate_money(trade_panel.liquidation, ref temp_float, ref temp_scale);
        dtl_liquidation.text = "Prix de Liquidation : " + temp_float.ToString() + temp_scale + " $";
        

    }

    public void SupprDetail()
    {
        details.SetActive(false);
    }

    public void Tout_Cloturer()
    {
        StartCoroutine(ClotureTrade(quantite.ToString().Replace(',', '.')));
    }

    public void Cloturer()
    {
        StartCoroutine(ClotureTrade(entree.text.ToString().Replace(',', '.')));
    }


    IEnumerator ClotureTrade(string quantite)
    {
        WWWForm form = new WWWForm();


        form.AddField("iduser", DBManager.id_user.ToString());
        form.AddField("idtrade", idtrade.ToString());
        form.AddField("quantite", quantite);
        form.AddField("password", DBManager.password);



        WWW www = new WWW("http://localhost/cryptoinc/cloture.php", form);
        yield return www;
        Debug.Log(www.text);
        if (int.Parse(www.text) == 0)
        {
            Debug.Log("reussi");
            UIManager.Actualise();
            SupprDetail();
            
        }
        else
        {
            Debug.Log(DBManager.error_list[int.Parse(www.text)]);
        }
    }



}
