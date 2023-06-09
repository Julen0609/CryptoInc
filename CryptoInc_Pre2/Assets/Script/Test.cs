using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using System;

public class Test : MonoBehaviour
{
    float argent_ref = 0;
    string argent_scale_ref;
    double argent_tot = 0.1;

    // Start is called before the first frame update
    void Start()
    {
        GameManager.instance.concatenate_money(argent_tot, ref argent_ref, ref argent_scale_ref);
        Debug.Log(argent_ref);
        Debug.Log(argent_scale_ref);

        DBManager.argent = double.Parse(("252562.24").Replace('.',','));

        //StartCoroutine(GetTradeInfos());
        //StartCoroutine(GetExchangeInfos());
        //StartCoroutine(GetContratInfos());
        StartCoroutine(GetPaireInfos());
        //StartCoroutine(GetActualiseInfos());
        StartCoroutine(ClotureTrade());

    }

    // Update is called once per frame
    void Update()
    {
        
    }

    IEnumerator GetTradeInfos()
    {
        WWWForm form = new WWWForm();
        form.AddField("iduser", "1");
        form.AddField("idcontrat", "1");
        form.AddField("quantite", "0.5");
        form.AddField("password", "password");
        form.AddField("transaction", "sell");
        WWW www = new WWW("http://localhost/cryptoinc/take_trade.php", form);
        yield return www;
        Debug.Log(www.text);
        string[] trade_infos = www.text.Split('\t');
        if (trade_infos[0] == "0")
        {
            Debug.Log("reussi");
        }
    }

    IEnumerator GetExchangeInfos()
    {
        WWW www = new WWW("http://localhost/cryptoinc/get_exch.php");
        yield return www;
        Debug.Log(www.text);
        string[] trade_infos = www.text.Split('\t');
        if (trade_infos[0] == "0")
        {
            Debug.Log("reussi");
        }
    }

    IEnumerator GetContratInfos()
    {
        WWWForm form = new WWWForm();
        form.AddField("exchange", "1");
        WWW www = new WWW("http://localhost/cryptoinc/get_contrat.php",form);
        yield return www;
        Debug.Log(www.text);
        string[] trade_infos = www.text.Split('\t');
        if (trade_infos[0] == "0")
        {
            Debug.Log("reussi");
        }
    }

    IEnumerator GetPaireInfos()
    {
        WWWForm form = new WWWForm();
        form.AddField("contrat", "3");
        form.AddField("user", "1");
        form.AddField("password", "password");
        WWW www = new WWW("http://localhost/cryptoinc/actualise_paire.php", form);
        yield return www;
        Debug.Log(www.text);
        string[] trade_infos = www.text.Split('\t');
        if (trade_infos[0] == "0")
        {
            Debug.Log("reussi");

            if(trade_infos[1] == "spot")
            {
                Debug.Log(trade_infos[2]);
                Debug.Log(trade_infos[3]);
                Debug.Log(trade_infos[4]);
                Debug.Log(trade_infos[5]);
                Debug.Log(trade_infos[6]);
            }
            else if (trade_infos[1] == "derivee")
            {
                Debug.Log(trade_infos[2]);
                Debug.Log(trade_infos[3]);
                Debug.Log(trade_infos[4]);
                Debug.Log(trade_infos[5]);
                Debug.Log(trade_infos[6]);
                string[] trade_list = trade_infos[7].Split('=');
                foreach (string trade in trade_list)
                {
                    string[] infos_trade = trade.Split('�');
                    Debug.Log("     trade : ");
                    Debug.Log(infos_trade[0]);
                    Debug.Log(infos_trade[1]);
                    Debug.Log(infos_trade[2]);
                    Debug.Log(infos_trade[3]);
                }
            }
        }
    }

    IEnumerator GetActualiseInfos()
    {
        WWWForm form = new WWWForm();
        form.AddField("idcontrat", "1");
        form.AddField("iduser", "1");
        form.AddField("password", "password");
        WWW www = new WWW("http://localhost/cryptoinc/actualise_exch.php", form);
        yield return www;
        Debug.Log(www.text);
        string[] trade_infos = www.text.Split('\t');
        if (trade_infos[0] == "0")
        {
            Debug.Log("reussi");
        }
    }

    IEnumerator ClotureTrade()
    {
        WWWForm form = new WWWForm();
        form.AddField("iduser", "1");
        form.AddField("idtrade", "1");
        form.AddField("quantite", "0.5");
        form.AddField("password", "password");
        WWW www = new WWW("http://localhost/cryptoinc/cloture.php", form);
        yield return www;
        Debug.Log(www.text);
        string[] trade_infos = www.text.Split('\t');
        Debug.Log("fini");
        if (trade_infos[0] == "0")
        {
            Debug.Log("reussi");
        }
    }

}
