using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;
using Unity.Mathematics;
using System;

public class GameManager : MonoBehaviour
{
    public static GameManager instance;


    Dictionary<int, string> convertion_table = 
        new Dictionary<int, string>();

    
    
    


    public void Awake()
    {
        if (instance != null)
        {
            Debug.LogError("Plus d'une instance du GameManager dans la scène !!");
            return;
            
        }
        instance = this;
        DontDestroyOnLoad(this);

        convertion_table.Add(0, "");
        convertion_table.Add(1, "K");
        convertion_table.Add(2, "M");
        convertion_table.Add(3, "B");
        convertion_table.Add(4, "T");

        DBManager.Start();

    }
    // Start is called before the first frame update
    void Start()
    {
        
    }

    // Update is called once per frame
    void Update()
    {
        
    }


    public float calcul_masse_monnaitaire(float argent, string argent_scale)
    {
        if (argent_scale == "K")
        {
            return argent * 1000;
        }
        else if (argent_scale == "M")
        {
            return argent * 1000000;
        }
        else if (argent_scale == "B")
        {
            return argent * Mathf.Pow(10, 9);
        }
        else if (argent_scale == "T")
        {
            return argent * Mathf.Pow(10, 12);
        }
        else
        {
            return argent;
        }
    }

    


    public void concatenate_money(double argent_total, ref float argent_ref, ref string argent_scale_ref)
    {
        int n = new();
        double test = argent_total;
        while (test > 1)
        {
            test = test / 10;
            n = n + 1;
        }
        n = n - 1;
        n = n - n % 3;
        int scale = n / 3;
        
        

        while (n > 0)
        {
            argent_total = argent_total / 10;
            Debug.Log(argent_total);
            Debug.Log(n);
            n = n - 1;
        }

        argent_ref = (float) Math.Round( argent_total,3) ;
        argent_scale_ref = convertion_table[scale];
        
        

    }
}
