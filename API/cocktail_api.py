import requests
import json
import os
import api_keys
url = "https://the-cocktail-db.p.rapidapi.com/"

#testDict = {"operation": "s",
#            "ingredient":"vodka" } 
headers = {
	"X-RapidAPI-Key": api_keys.api_key, # please keep the 100/day limit in mind for the Cocktail DB, if you are working on it 
    "X-RapidAPI-Host": "the-cocktail-db.p.rapidapi.com",                    # just input your api key so you can keep track of calls
	"Content-Type": "application/json"                                                                      
}
def to_json_file(response,fileName):
    # add response to json file
    os.chdir('json_cache_files/')
    cwd = os.getcwd() + "/"
    fileName = "/" + fileName 
    path = cwd + fileName
    print("Results count: " +str(len(response['drinks'])))
    
    with open(path,"w") as write_file:
        #json.dump(results_count, write_file)
        json.dump(response, write_file, indent=2)

def search_by_name(dictionary):
    # returns all cocktails with Ingredient in the name ex Vodka -> Vodka fizz, Vodka Martini
    url = "https://the-cocktail-db.p.rapidapi.com/search.php"
    
    querystring = {dictionary['operation']:dictionary['ingredient']}
    print(querystring)
    response = requests.request("GET", url, headers=headers, params=querystring)

    #make a call to json to file function to cache data
    to_json_file(response.json(),"vodka_search_results.json")

    # turns response to json and prints it nicely
    response = response.json()
    #print(json.dumps(response, indent=2))


def search_by_single_ingredient(dictionary):
    # Paul
    # return all cocktails with that include this ingredient ex Vodka -> 102 items that contain Vodka as an ingredient
    # Only returns Drink name, Drink Pic, Drink ID
    # example query string querystring = {"i":"Vodka"}

    url = "https://the-cocktail-db.p.rapidapi.com/filter.php"
    querystring = {}
    response = requests.request("GET", url, headers=headers, params=querystring)
    response = response.json()
    

def get_cocktail_details_by_id(id):
    # Matt
    # returns full details for cocktail by its ID
    # example query string querystring = {"i":"11007"}

    url = "https://the-cocktail-db.p.rapidapi.com/lookup.php"
    querystring = {}
    response = requests.request("GET", url, headers=headers, params=querystring)
    response = response.json()


def random_10_cocktails():
    # Paul
    # returns all details about these 10 random cocktails
    # example query string N/A

    url = "https://the-cocktail-db.p.rapidapi.com/randomselection.php"
    response = requests.request("GET", url, headers=headers)
    response = response.json()


def filter_by_category(dictionary):
    # Jon
    # returns Drinks of that Type such as Cocktails, Oridinary Drink, etc
    # example query string querystring = {"c":"Cocktail"}

    url = "https://the-cocktail-db.p.rapidapi.com/filter.php"
    querystring = {}
    response = requests.request("GET", url, headers=headers, params=querystring)
    response = response.json()


def filter_by_multi_ingredient(dictionary):
    # Paul
    # returns drinks with the input ingredients in their details ex Dry_Vermouth,Gin,Anis -> returns Drink Name, image, ID
    # example query string querystring = {"i":"Dry_Vermouth,Gin,Anis"}

    url = "https://the-cocktail-db.p.rapidapi.com/filter.php"
    querystring = {}
    response = requests.request("GET", url, headers=headers, params=querystring)
    response = response.json()


def list_ingredients():
    # done 
    # returns all ingredients
    # example query string N/A

    url = "www.thecocktaildb.com/api/json/v1/1/list.php?i=list"
    response = requests.request("GET", url, headers=headers)
    response = response.json()


def list_popular_cocktails():
    # Done
    # returns same 20 cocktails that are "popular"
    # example query string N/A

    url = "https://the-cocktail-db.p.rapidapi.com/popular.php"
    response = requests.request("GET", url, headers=headers)
    response = response.json()


def search_ingredient_info(dictionary):
    # Justin
    # return information about input ingredient ex: Gin -> will return what gin is and how it is made
    # example query string querystring = {"i":"vodka"}

    url = "https://the-cocktail-db.p.rapidapi.com/search.php"
    querystring = {}
    response = requests.request("GET", url, headers=headers, params=querystring)
    response = response.json()


testDict = {"operation": "s",
           "ingredient":"vodka" }
search_by_name(testDict)




