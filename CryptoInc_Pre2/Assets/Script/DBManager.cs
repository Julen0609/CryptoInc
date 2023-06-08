using System.Collections;
using System.Collections.Generic;
using UnityEngine;

public static class DBManager
    
{
    public static string username;
    public static double argent;
    public static ulong id_user;
    public static string password;

    public static Dictionary<int, string> error_list = new Dictionary<int, string>();

    public static void Start()
    {
        error_list.Add(1, "Echec de la connection");
        error_list.Add(2, "Echec de la recuperation des données");
        error_list.Add(3, "L'utilisateur existe deja");
        error_list.Add(4, "Echec de l'insertion des données");
        error_list.Add(5, "Utilisateur inexistant ou plus d'un dans la base de donnée");
        error_list.Add(6, "Mot de passe incorrect");
        error_list.Add(7, "");
        error_list.Add(8, "Caractères non autorisès dans le champs d'entrée");
        error_list.Add(9, "Pas assez d'argent");
        error_list.Add(10, "Echec de l'upadate");
        error_list.Add(11, "Pas assez de supply");
        error_list.Add(12, "Au dessus de la supply max");
        error_list.Add(13, "Pas assez de crypto");
        error_list.Add(14, "Mauvais type de trade");
        error_list.Add(15, "Mauvais levier");
        error_list.Add(16, "L'utilisateur qui fait l'action n'est pas le proprietaire du trade");
        error_list.Add(17, "Le trade recherché n'existe plus");
        error_list.Add(18, "Le trade n'est pas un derivée");
    }

    public static bool LoggedIn { get { return username != null; } }

    public static void LogOut()
    {
        username = null;
    }
    // Start is called before the first frame update

}
