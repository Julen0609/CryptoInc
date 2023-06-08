using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;
using System;

public class levier : MonoBehaviour
{
    public Slider leverage_slider;
    public Text leverage_txt;
    public int leverage_max;
    public int leverage;


    public void Actualize(int levier_max)
    {
        leverage_max = levier_max;
        Slideactualize();
    }

    public void Slideactualize()
    {
        Debug.Log(leverage_max);

        leverage = (int)Math.Round(leverage_slider.value * leverage_max);
        if (leverage >= 1)
        {
            
            leverage_txt.text = "x " + leverage.ToString();
        }
        else
        {
            leverage = 1;
            leverage_txt.text = "x " + leverage.ToString();
        }
            
    }

}
