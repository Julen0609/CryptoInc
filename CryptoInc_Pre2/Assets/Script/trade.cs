using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;
using System;

public class trade : MonoBehaviour
{
    public GameObject all;
    public Text profit_txt;
    public Text quantite_txt;
    public Text type_txt;

    public trade_list trade_List;

    public UIManager UIManager;

    public TradeData data;

    public int leverage;
    public double prix;
    public string type_trade;
    public double initial_invest;

    public string temp_scale;
    public float temp_float;


    // Start is called before the first frame update
    void Start()
    {
        
    }

    // Update is called once per frame
    void Update()
    {
        
    }

    public void Detail()
    {
        trade_List.Detail(data.id,this);
    }

    public void Actualise()
    {

        GameManager.instance.concatenate_money(data.quantite, ref temp_float, ref temp_scale);
        quantite_txt.text = temp_float.ToString() + temp_scale + " " +  UIManager.contrat.sigle;

        var profit_dollard = new double();
        var profit_percent = new double();
        var roi = new double();

        if (data.etat.Contains("long")){
            type_trade = "Long";
        } else if (data.etat.Contains("shrt"))
        {
            type_trade = "Short";
        }
        leverage = int.Parse(data.etat.Substring(4));


        if (type_trade == "Long")

        {
            roi = prix * data.quantite - data.prix_achat * data.quantite * (1f - 1f / leverage);
            initial_invest = data.prix_achat * data.quantite / leverage;
            profit_dollard = roi - initial_invest;
            profit_percent = roi / initial_invest * 100;
        }
        else if (type_trade == "Short")
        {
            roi = data.prix_achat * data.quantite - prix * data.quantite * (1f - 1f / leverage);
            initial_invest = prix * data.quantite / leverage;
            profit_dollard = roi - initial_invest;
            profit_percent = roi / initial_invest * 100;
        }

        profit_txt.text = (profit_dollard).ToString();
        type_txt.text = type_trade;
    }

    public void Dstr()
    {
        Destroy(all);
    }

}

[Serializable]
public class TradeData
{
    public double quantite;
    public string etat;
    public double id;
    public double prix_achat;
    public double liquidation;
}
