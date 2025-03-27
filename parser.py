import requests
from bs4 import BeautifulSoup
import json

url = "https://yandex.ru/pogoda?from=tableau_yabro"

response = requests.get(url)
soup = BeautifulSoup(response.text, 'html.parser')

temp_value = soup.find('span', class_='temp__value')
temp_value_with_unit = soup.find('span', class_='temp__value temp__value_with-unit')

temp_value_text = temp_value.text if temp_value else None
temp_value_with_unit_text = temp_value_with_unit.text if temp_value_with_unit else None

data = {
    "temp_value": temp_value_text,
    "temp_value_with_unit": temp_value_with_unit_text
}

# Записываем данные в JSON файл
with open('weather_data.json', 'w') as json_file:
    json.dump(data, json_file, ensure_ascii=False, indent=4)

print("Данные успешно записаны в weather_data.json")
