using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;

public class trade : MonoBehaviour
{
    public GameObject all;
    public Text profit_txt;
    public Text quantite_txt;
    public Text type_txt;

    public trade_list trade_List;

    public UIManager UIManager;



    public double quantite;
    public double prix_dachat;
    public int leverage;
    public double id_trade;
    public double prix;
    public string type_trade;
    public double liquidation;
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
        trade_List.Detail(id_trade,this);
    }

    public void Actualise()
    {

        GameManager.instance.concatenate_money(quantite, ref temp_float, ref temp_scale);
        quantite_txt.text = temp_float.ToString() + temp_scale + " " +  UIManager.crypto_sigle;

        var profit_dollard = new double();
        var profit_percent = new double();
        var roi = new double();


        if (type_trade == "Long")

        {
            roi = prix * quantite - prix_dachat * quantite * (1f - 1f / leverage);
            initial_invest = prix_dachat * quantite / leverage;
            profit_dollard = roi - initial_invest;
            profit_percent = roi / initial_invest * 100;
        }
        else if (type_trade == "Short")
        {
            roi = prix_dachat * quantite - prix * quantite * (1f - 1f / leverage);
            initial_invest = prix * quantite / leverage;
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
