using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;

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
    public Dropdown Exchange;
    public Dropdown Contrat;
    public GameObject list_trade_code;
    public GameObject levier;

    Dictionary<int, string[] > exchanges_list = new Dictionary<int, string[]>();
    Dictionary<int, string[]> contrat_list = new Dictionary<int, string[]>();

    //public List<trade> trades = new List<trade>();
    public trade_list list_trade;
    public levier levier_code;


    public double argent = 1563;
    public double prix_crypto = 29000;
    public string crypto_name = "Bitcoin";
    public string crypto_sigle = "Btc";
    public string exchange_name = "Binance";
    public string exchange_id = "1";
    public double hold = 0;
    public double prix_relatif = 0;
    public float temp_float;
    public string temp_scale;
    public string contrat_id = "1";
    public double crypto_supply = 0;
    public double quantite = 0;
    public int leverage_max;
    public string type_contrat;

    // Start is called before the first frame update
    void Start()
    {
        argent = DBManager.argent ;
        GameManager.instance.concatenate_money(argent, ref temp_float, ref temp_scale);

        prix_crypto_txt.text = prix_crypto.ToString()+" $";
        argent_txt.text = temp_float.ToString()+"  "+ temp_scale + " $";
        crypto_name_txt.text = crypto_name;
        crypto_sigle_txt.text = crypto_sigle;
        exchange_name_txt.text = exchange_name;
        change_txt.text = crypto_sigle;
        prix_relatif_txt.text = "0 $";
        hold_txt.text = hold.ToString()+" "+ crypto_sigle;
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
            prix_relatif = (quantite * prix_crypto/ levier_code.leverage);
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

    
        DBManager.argent = argent;
        GameManager.instance.concatenate_money(argent, ref temp_float, ref temp_scale);

        prix_crypto_txt.text = prix_crypto.ToString() + " $"; //
        argent_txt.text = temp_float.ToString() + "  " + temp_scale + " $"; //
        crypto_name_txt.text = crypto_name;
        crypto_sigle_txt.text = crypto_sigle;
        exchange_name_txt.text = exchange_name;
        change_txt.text = crypto_sigle;
        hold_txt.text = hold.ToString() + " " + crypto_sigle; //
        crypto_relative_txt.text = "0";
        quantite = 0;
        prix_relatif = 0;
        prix_relatif_txt.text = "0 $";

    }


    public void Buy()
    {
        if (argent >= prix_relatif)
        {
            if (type_contrat == "spot")
            {
                StartCoroutine(TakeTrade("buy"));
            } else  if (type_contrat == "derivee")
            {
                StartCoroutine(TakeTrade("long"));
            }
            
        }
    }

    public void Sell()
    {
            if (type_contrat == "spot")
            {
                StartCoroutine(TakeTrade("sell"));
            }
            else if (type_contrat == "derivee")
            {
                StartCoroutine(TakeTrade("short"));
            }
    }


    public void Change_Exchange()
    {
        exchange_name = exchanges_list[Exchange.value][0];
        exchange_id = exchanges_list[Exchange.value][1];
        exchange_name_txt.text = exchange_name;
        StartCoroutine(GetContratInfos());
    }

    public void Change_Paire()
    {
        contrat_id = contrat_list[Contrat.value][1];
        crypto_sigle = contrat_list[Contrat.value][0];
        crypto_sigle_txt.text = crypto_sigle;
        Actualise();
    }


    IEnumerator GetExchangeInfos()
    {

        WWW www = new WWW("http://localhost/cryptoinc/get_exch.php");
        yield return www;
        Debug.Log(www.text);
        string[] reponse = www.text.Split('\t');
        if (reponse[0] == "0")
        {
            Debug.Log("reussi");
            List<string> exchange = new List<string> { };
            exchanges_list.Clear();
            for (int i = 1; i < (reponse.Length - 1); i++)
            {
                string[] sliced = reponse[i].Split('=');

                Debug.Log(sliced[0] + sliced[1]);
                exchange.Add(sliced[0]);
                exchanges_list.Add(i-1, new string[] { sliced[0], sliced[1] });
                
            }
            Exchange.ClearOptions();
            Exchange.AddOptions((exchange));
            Change_Exchange();
        }
        else
        {
            Debug.Log(DBManager.error_list[int.Parse(www.text)]);
        }

    }

    IEnumerator GetContratInfos()
    {

        WWWForm form = new WWWForm();
        Debug.Log(exchange_name);
        form.AddField("exchange", exchange_id);
        WWW www = new WWW("http://localhost/cryptoinc/get_contrat.php", form);
        yield return www;
        Debug.Log(www.text);
        string[] reponse = www.text.Split('\t');
        if (reponse[0] == "0")
        {
            Debug.Log("reussi");
            List<string> contrat = new List<string> { };
            contrat_list.Clear();
            for (int i = 1; i < (reponse.Length - 1); i++)
            {
                string[] sliced = reponse[i].Split('=');

                Debug.Log(sliced[0] + sliced[1]);
                contrat.Add(sliced[0]);
                contrat_list.Add(i - 1, new string[] { sliced[0], sliced[1] });
                
            }
            Contrat.ClearOptions();
            Contrat.AddOptions((contrat));
            Change_Paire();
        }
        else
        {
            Debug.Log(DBManager.error_list[int.Parse(www.text)]);
        }
    }


    IEnumerator TakeTrade(string sens)
    {
        WWWForm form = new WWWForm();

    
        form.AddField("iduser", DBManager.id_user.ToString());
        form.AddField("idcontrat", contrat_id.ToString());
        form.AddField("quantite", quantite.ToString().Replace(',', '.'));
        form.AddField("password", DBManager.password);
        form.AddField("transaction", sens);
        if (sens == "long" || sens == "short")
        {
            form.AddField("levier", levier_code.leverage);
        }


        Debug.Log("TradeTaken");

        WWW www = new WWW("http://localhost/cryptoinc/take_trade.php", form);
        yield return www;
        Debug.Log(www.text);
        if (int.Parse(www.text) == 0)
        {
            Debug.Log("reussi");
            Actualise();
        }
        else
        {
            Debug.Log(DBManager.error_list[int.Parse(www.text)]);
        }
    }


    IEnumerator ActualiseInfos()
    {
        WWWForm form = new WWWForm();
        form.AddField("contrat", contrat_id);
        form.AddField("user", DBManager.id_user.ToString());
        form.AddField("password", DBManager.password);
        WWW www = new WWW("http://localhost/cryptoinc/actualise_paire.php", form);
        yield return www;
        Debug.Log(www.text);
        string[] reponse = www.text.Split('\t');
        if (reponse[0] == "0")
        {
            type_contrat = reponse[1];
            if (reponse[1] == "spot")
            {
                prix_crypto = double.Parse(reponse[2].Replace('.', ','));
                argent = double.Parse(reponse[3].Replace('.', ','));
                crypto_name = reponse[4];
                crypto_supply = double.Parse(reponse[5].Replace('.', ','));
                hold = double.Parse(reponse[6].Replace('.', ','));
                list_trade.Clear();
                list_trade_code.SetActive(false);
                levier.SetActive(false);
            }
            else if (reponse[1] == "derivee")
            {
                prix_crypto = double.Parse(reponse[2].Replace('.', ','));
                argent = double.Parse(reponse[3].Replace('.', ','));
                crypto_name = reponse[4];
                crypto_supply = double.Parse(reponse[5].Replace('.', ','));
                leverage_max = int.Parse(reponse[6]);

                list_trade_code.SetActive(true);
                string[] trade_list = reponse[7].Split('=');

                if (trade_list[0].Contains("°"))
                {
                    list_trade.Initialize(trade_list.Length);
                }
                else
                {
                    list_trade.Clear();
                }
                if (leverage_max > 1)
                {
                    levier.SetActive(true);
                    levier_code.Actualize(leverage_max);
                }
                else
                {
                    levier.SetActive(false);
                }
                if (trade_list[0].Contains("°"))
                {
                    int n = 0;
                    foreach (string trade in trade_list)
                    {
                        string[] infos_trade = trade.Split('°');
                        list_trade.list_trade[n].id_trade = double.Parse(infos_trade[0]);
                        list_trade.list_trade[n].prix_dachat = double.Parse(infos_trade[1].Replace('.', ','));
                        list_trade.list_trade[n].quantite = double.Parse(infos_trade[2].Replace('.', ','));
                        list_trade.list_trade[n].leverage = int.Parse(infos_trade[3]);
                        list_trade.list_trade[n].prix = prix_crypto;
                        list_trade.list_trade[n].type_trade = infos_trade[4];
                        list_trade.list_trade[n].liquidation = double.Parse(infos_trade[5].Replace('.', ','));
                        list_trade.list_trade[n].UIManager = this;
                        list_trade.list_trade[n].Actualise();
                        n = n + 1;
                    }
                }
                
                
                

                hold = 0;

            }


            Debug.Log("reussi");

            Actualise2();
        }
        else
        {
            Debug.Log(DBManager.error_list[int.Parse(www.text)]);
        }

    }


}
