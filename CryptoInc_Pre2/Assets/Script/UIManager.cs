using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;
using UnityEngine.Networking;
using System;

public class UIManager : MonoBehaviour
{
  

    public Text prix_crypto_txt;
    public Text argent_txt;
    public Text crypto_name_txt;
    public Text crypto_sigle_txt;
    public Text exchange_name_txt;
    public Text change_txt;
    public Text prix_relatif_txt;
    public InputField crypto_relative_txt;
    public Text hold_txt;
    public Dropdown Exchange_Drpdn;
    public Dropdown Contrat_Drpdn;
    public GameObject list_trade_GObj;
    public GameObject levier_GObj;

    public Paire paire;

    public Exchange_List exchanges_list;
    public Exchange exchange;   //Exchange Actuel
    public Contrat_List contrat_list;
    public Contrat contrat;     //Contrat Actuel

    public levier levier_code;

    public double prix_relatif = 0;
    public double quantite = 0;
    public float temp_float;
    public string temp_scale;
    

    // Start is called before the first frame update
    void Start()
    {
        //argent = DBManager.connect.argent ;
        GameManager.instance.concatenate_money(DBManager.connect.argent, ref temp_float, ref temp_scale);

        prix_crypto_txt.text = paire.data.prix.ToString()+" $";
        argent_txt.text = temp_float.ToString()+"  "+ temp_scale + " $";
        crypto_name_txt.text = paire.data.name;
        crypto_sigle_txt.text = contrat.sigle;
        exchange_name_txt.text = exchange.name;
        change_txt.text = contrat.sigle;
        prix_relatif_txt.text = "0 $";
        hold_txt.text = paire.hold.ToString()+" "+ contrat.sigle;
        crypto_relative_txt.text = "0";
        quantite = 0;
        prix_relatif = 0;

        StartCoroutine(GetExchangeInfos());
        
        
    }

    // Update is called once per frame
    void Update()
    {
        
    }

    public void ChangeValue()
    {
        var refs = 0f;
        if (float.TryParse(crypto_relative_txt.text, out refs))
        {
            quantite = double.Parse(crypto_relative_txt.text);
            prix_relatif = (quantite * paire.data.prix / levier_code.leverage);
            GameManager.instance.concatenate_money(prix_relatif, ref temp_float, ref temp_scale);
            prix_relatif_txt.text = temp_float.ToString() + temp_scale + " $";
        }
        else
        {
            Debug.Log("Error, not number valid");
        }

    }

    public void Actualise()
    {

        StartCoroutine(ActualiseInfos());


    }
    public void Actualise2()
    {

        GameManager.instance.concatenate_money(DBManager.connect.argent, ref temp_float, ref temp_scale);
        argent_txt.text = temp_float.ToString() + "  " + temp_scale + " $"; //

        GameManager.instance.concatenate_money(paire.data.prix, ref temp_float, ref temp_scale);
        prix_crypto_txt.text = temp_float.ToString() + "  " + temp_scale + " $"; //

        GameManager.instance.concatenate_money(paire.hold, ref temp_float, ref temp_scale);
        hold_txt.text = temp_float.ToString() + "  " + temp_scale + " " + contrat.sigle; //

        crypto_name_txt.text = paire.data.name;
        crypto_sigle_txt.text = contrat.sigle;
        exchange_name_txt.text = exchange.name; 
        change_txt.text = contrat.sigle;
        
        crypto_relative_txt.text = "0";
        prix_relatif_txt.text = "0 $";

        quantite = 0;
        prix_relatif = 0;
        

    }


    public void Buy()
    {
        if (DBManager.connect.argent >= prix_relatif)
        {
            if (paire.data.type == "spot")
            {
                StartCoroutine(TakeTrade("buy"));
            } else  if (paire.data.type == "derivee")
            {
                StartCoroutine(TakeTrade("long"));
            }
            
        }
    }

    public void Sell()
    {
            if (paire.data.type == "spot")
            {
                StartCoroutine(TakeTrade("sell"));
            }
            else if (paire.data.type == "derivee")
            {
                StartCoroutine(TakeTrade("short"));
            }
    }


    public void Change_Exchange()
    {
        exchange = exchanges_list.list[Exchange_Drpdn.value];
        exchange_name_txt.text = exchange.name;
        StartCoroutine(GetContratInfos());
    }

    public void Change_Paire()
    {
        contrat = contrat_list.list[Contrat_Drpdn.value];
        crypto_sigle_txt.text = contrat.sigle;
        Actualise();
    }


    IEnumerator GetExchangeInfos()
    {
        
        WWWForm form = new WWWForm();
        UnityWebRequest www = UnityWebRequest.Post("http://localhost:5000/get_exch", form);
        www.downloadHandler = new DownloadHandlerBuffer();
        yield return www.SendWebRequest();
        Debug.Log(www.downloadHandler.text);
        string[] reponse = www.downloadHandler.text.Split('\t');
        if (reponse[0] == "0")
        {
            Debug.Log(reponse[1]);
            exchanges_list = JsonUtility.FromJson<Exchange_List>(reponse[1]);
            Debug.Log(exchanges_list.list[0]);
            List<string> exch_name = new List<string>();
            foreach (Exchange exchange in exchanges_list.list)
            {
                exch_name.Add(exchange.name);
            }
            Exchange_Drpdn.ClearOptions();
            Exchange_Drpdn.AddOptions((exch_name));
            Change_Exchange();
        }
        else
        {
            Debug.Log(DBManager.error_list[int.Parse(www.downloadHandler.text)]);
        }

    }

    IEnumerator GetContratInfos()
    {

        WWWForm form = new WWWForm();
        Debug.Log(exchange.name);
        form.AddField("exchange", exchange.id.ToString());
        UnityWebRequest www = UnityWebRequest.Post("http://localhost:5000/get_contrat", form);
        www.downloadHandler = new DownloadHandlerBuffer();
        yield return www.SendWebRequest();
        Debug.Log(www.downloadHandler.text);
        string[] reponse = www.downloadHandler.text.Split('\t');
        if (reponse[0] == "0")
        {
            contrat_list = JsonUtility.FromJson<Contrat_List>(reponse[1]);
            List<string> contrat_sigles = new List<string>();
            foreach (Contrat contrat in contrat_list.list)
            {
                contrat_sigles.Add(contrat.sigle);
            }
            Contrat_Drpdn.ClearOptions();
            Contrat_Drpdn.AddOptions((contrat_sigles));
            Change_Paire();
        }
        else
        {
            Debug.Log(DBManager.error_list[int.Parse(www.downloadHandler.text)]);
        }
    }


    IEnumerator TakeTrade(string sens)
    {
        WWWForm form = new WWWForm();

    
        form.AddField("iduser", DBManager.connect.id.ToString());
        form.AddField("idcontrat", contrat.id.ToString());
        form.AddField("quantite", quantite.ToString().Replace(',', '.'));
        form.AddField("password", DBManager.connect.password);
        form.AddField("transaction", sens);
        if (sens == "long" || sens == "short")
        {
            form.AddField("levier", levier_code.leverage);
        }


        Debug.Log("TradeTaken");

        UnityWebRequest www = UnityWebRequest.Post("http://localhost:5000/take_trade", form);
        www.downloadHandler = new DownloadHandlerBuffer();
        yield return www.SendWebRequest();
        Debug.Log(www.downloadHandler.text);
        if (www.downloadHandler.text == "0")
        {
            Debug.Log("reussi");
            Actualise();
        }
        else
        {
            Debug.Log(DBManager.error_list[int.Parse(www.downloadHandler.text)]);
        }
    }


    IEnumerator ActualiseInfos()
    {
        WWWForm form = new WWWForm();
        form.AddField("contrat", contrat.id.ToString());
        form.AddField("user", DBManager.connect.id.ToString());
        form.AddField("password", DBManager.connect.password);
        UnityWebRequest www = UnityWebRequest.Post("http://localhost:5000/actualise_paire", form);
        www.downloadHandler = new DownloadHandlerBuffer();
        yield return www.SendWebRequest();
        Debug.Log(www.downloadHandler.text);
        string[] reponse = www.downloadHandler.text.Split('\t');
        if (reponse[0] == "0")
        {
            paire.data = JsonUtility.FromJson<JSONPaireData>(reponse[1]);
            paire.temp_trades = JsonUtility.FromJson<JSONTrade_List>(reponse[2]);
            DBManager.connect.argent = double.Parse(reponse[3].Replace('.', ','));
            paire.Actualise(this);


            Debug.Log("reussi");

            Actualise2();//Continuer avec Actualise 2
        }
        else
        {
            Debug.Log(DBManager.error_list[int.Parse(www.downloadHandler.text)]);
        }

    }


}

[Serializable]
public class Exchange
{
    public ulong id;
    public string name;
}
[Serializable]
public class Exchange_List
{
    public List<Exchange> list;
}

[Serializable]
public class Contrat
{
    public ulong id;
    public string sigle;
}
[Serializable]
public class Contrat_List
{
    public List<Contrat> list;
}
[Serializable]
public class Paire
{
    public JSONPaireData data;
    public JSONTrade_List temp_trades;
    public trade_list trade_list;   
    public double hold;

    public void Actualise(UIManager uIManager   )
    {
        if (data.type == "spot")
        {
            hold = 0;
            foreach (TradeData hld in temp_trades.list)
            {
                hold = hold + hld.quantite;
            }
            uIManager.list_trade_GObj.SetActive(false);
            uIManager.levier_GObj.SetActive(false);
        }
        else if (data.type == "derivee")
        {
            int n = 0;
            if (data.leverage_max > 1)
            {
                uIManager.levier_GObj.SetActive(true);
                uIManager.levier_code.Actualize(data.leverage_max);
            }
            else
            {
                uIManager.levier_GObj.SetActive(false);
            }

            uIManager.list_trade_GObj.SetActive(true);
            if (temp_trades.list.Count >= 1)
            {
                trade_list.Initialize(temp_trades.list.Count);
            }
            else
            {
                trade_list.Clear();
                uIManager.list_trade_GObj.SetActive(false);
            }
            foreach (TradeData trd in temp_trades.list)
            {

                trade_list.list[n].data = trd;
                trade_list.list[n].UIManager = uIManager;
                trade_list.list[n].prix = data.prix;
                trade_list.list[n].Actualise();
                n = n + 1;
            }
            hold = 0;
        }
    }

}

[Serializable] 
public class JSONTrade_List
{
    public List<TradeData> list;
}
[Serializable]

public class JSONPaireData
{
    public double prix;
    public double supply;
    public string name;
    public string type;
    public int leverage_max;
}