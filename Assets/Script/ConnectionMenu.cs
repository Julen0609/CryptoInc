using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;

public class ConnectionMenu : MonoBehaviour
{
    public InputField username;
    public InputField password;

    public Button LoginButton;
    public Button RegisterButton;

    public void callLogIn()
    {
        StartCoroutine(Login());
    }

    public void callRegister()
    {
        StartCoroutine(Register());
    }

    IEnumerator Login()
    {
        WWWForm form = new WWWForm();
        form.AddField("name", username.text);
        form.AddField("password", password.text);
        WWW www = new WWW("http://localhost/cryptoinc/login.php", form);
        yield return www;
        Debug.Log(www.text);
        string[] user_infos = www.text.Split('\t');
        if (user_infos[0] == "0")
        {
            

            DBManager.id_user = ulong.Parse(user_infos[2]);
            DBManager.username = username.text;
            DBManager.argent = double.Parse((user_infos[1]).Replace('.',','));
            DBManager.password = password.text;
            UnityEngine.SceneManagement.SceneManager.LoadScene(1);
        }
        else
        {
            Debug.Log("User Login failed. Error #" + www.text);
        }
        Debug.Log(www.text);
    }

    IEnumerator Register()
    {
        WWWForm form = new WWWForm();
        form.AddField("name", username.text);
        form.AddField("password", password.text);
        WWW www = new WWW("http://localhost/cryptoinc/register.php", form);
        yield return www;
        if (www.text == "0")
        {
            Debug.Log("User Created Sucessfully");
        }
        else
        {
            Debug.Log("User creation Failed. Error #" + www.text);
        }
    }

}




