#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Created on Thu Feb  1 09:05:06 2024

@author: djentwistle
"""

import geopandas as gpd
import pandas as pd
from geopy.geocoders import Nominatim
from shapely.geometry import Point
import matplotlib.pyplot as plt
import folium
from folium.plugins import HeatMap

# Define the get_lat_long function
def get_lat_long(city, state):
    geolocator = Nominatim(user_agent="my_geocoder")
    location = geolocator.geocode(f"{city}, {state}")
    
    if location:
        return location.latitude, location.longitude
    else:
        return None

# Define the data variable
data = ['Belgian Malinois Puppy, Ruby, $1500.00, New Holland, Pa,', 
        'Belgian Malinois Puppy, Meek, $1700.00, Gap, PA,',
        'Belgian Malinois Puppy, Alicia, $700.00, Myerstown, PA,', 
        'Belgian Malinois Puppy, Simba, $800.00, Honey Brook, PA,',
        'Belgian Malinois Puppy, Sarge, $800.00, Honey Brook, PA,', 
        'Belgian Malinois Puppy, Abbott, $850.00, Myerstown, PA,', 
        'Belgian Malinois Puppy, Kayleen, $1150.00, Narvon, PA,', 
        'Belgian Malinois Puppy, Alisha, $1500.00, Honey Brook, PA,', 
        'Belgian Malinois Puppy, Sparkle, $800.00, Honey Brook, PA,', 
        'Belgian Malinois Puppy, Radar, $1500.00, New Holland, Pa,', 
        'Belgian Malinois Puppy, Skye, $800.00, Honey Brook, PA,',
        'Belgian Malinois Puppy, Avery, $1500.00, Honey Brook, PA,', 
        'Belgian Malinois Puppy, Denise, $650.00, Laredo, TX,', 
        'Belgian Malinois Puppy, Milly, $1700.00, Gap, PA,', 
        'Belgian Malinois Puppy, Arnie, $1500.00, Honey Brook, PA,', 
        'Belgian Malinois Puppy, Abby, $1500.00, Honey Brook, PA,', 
        'Belgian Malinois Puppy, Apollo, $1500.00, Honey Brook, PA,', 
        'Belgian Malinois Puppy, Mini, $1900.00, Gap, PA,', 
        'Belgian Malinois Puppy, Simba, $1495.00, Wayne, NJ,', 
        'Belgian Malinois Puppy, Marlee, $850.00, Honey Brook, PA,', 
        'Belgian Malinois Puppy, Spice, $800.00, Honey Brook, PA,', 
        'Belgian Malinois Puppy, Moose, $1700.00, Gap, PA,', 
        'Belgian Malinois Puppy, Brie, $600.00, Lebanon, PA,', 
        'Belgian Malinois Puppy, Strike, $800.00, Honey Brook, PA,', 
        'Belgian Malinois Puppy, Dixie, $500.00, Morgantown, PA,', 
        'Belgian Malinois Puppy, Kendi, $1150.00, Narvon, PA,', 
        'Belgian Malinois Puppy, Nala, $1495.00, Wayne, NJ,', 
        'Belgian Malinois Puppy, Splash, $800.00, Honey Brook, PA,', 
        'Belgian Malinois Puppy, Bailey, $600.00, Lebanon, PA,', 
        'Belgian Malinois Puppy, Serena, $800.00, Honey Brook, PA,', 
        'Belgian Malinois Puppy, Darla, $500.00, Morgantown, PA,', 
        'Belgian Malinois Puppy, Delta, $500.00, Morgantown, PA,', 
        'Belgian Malinois Puppy, Sarabi, $1495.00, Wayne, NJ,', 
        'Belgian Malinois Puppy, Amber, $1500.00, Honey Brook, PA,', 
        'Belgian Malinois Puppy, Diva, $500.00, Morgantown, PA,', 
        'Belgian Malinois Puppy, Sarafina, $1495.00, Wayne, NJ,', 
        'Belgian Malinois Puppy, Maverick, $1700.00, Gap, PA,', 
        'Belgian Malinois Puppy, Special, $800.00, Honey Brook, PA,', 
        'Belgian Malinois Puppy, Ace, $1500.00, Honey Brook, PA,', 
        'Belgian Malinois Puppy, Allie, $1500.00, Honey Brook, PA']

# Create an empty DataFrame
df = pd.DataFrame(columns=['breed', 'name', 'price', 'city', 'state', 'Latitude', 'Longitude'])

# Iterate through the list of strings and add each item as a new row in the DataFrame
for item in data:
    # Split the string by the comma
    item_list = item.split(',')
    
    # Check if the item starts with 'Belgian Malinois Puppy'
    if item_list[0].startswith('Belgian Malinois Puppy'):
        # Extract information and add to the DataFrame
        breed = item_list[0]
        name = item_list[1]
        price = item_list[2].strip()
        location = item_list[3] + "," + item_list[4]
        city, state = location.strip().split(',')
        
        # Use the get_lat_long function to get coordinates
        coordinates = get_lat_long(city, state)
        
        # Add data to DataFrame
        df = df._append({'breed': breed, 'name': name, 'price': price, 
                        'city': city, 'state': state, 
                        'Latitude': coordinates[0] if coordinates else None, 
                        'Longitude': coordinates[1] if coordinates else None}, 
                       ignore_index=True)

#%%

# Save DataFrame to CSV with only 'Latitude' and 'Longitude'
output_path = '/Users/djentwistle/Downloads/gps2shp/output.csv'
df[['Latitude', 'Longitude']].to_csv(output_path, index=False)
# df.rename(columns={'Longitude': 'lng', 'Latitude': 'lat'}).to_csv(output_path, index=False)

#Do I want the lat and long labelled as so?
#Read the data from the output csv and then make the data points into maps


# Load the DataFrame from the CSV file
df = pd.read_csv('/Users/djentwistle/Downloads/gps2shp/output.csv')

# Create a map centered at a specific location
m = folium.Map(location=[40.0, -80.0], zoom_start=6)  # You can adjust the center and zoom level

# Ensure the coordinates are not NaN
df.dropna(subset=['Latitude', 'Longitude'], inplace=True)

# Convert data to list of tuples
locations = df[['Latitude', 'Longitude']].values.tolist()

# Add heatmap layer
HeatMap(locations).add_to(m)

m.show()

# Save the map as an HTML file
# m.save('/Users/djentwistle/Downloads/gps2shp/heatmap.html')

