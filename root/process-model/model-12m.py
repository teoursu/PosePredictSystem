import sys
import json
import warnings

import numpy as np
import pandas as pd
import joblib as joblib
import csv
import json
import numpy as np

def normalize_dataset(dataset, min_values, max_values, excluded_fields):
    
    normalized_dataset = []

    for data in dataset:
        normalized_data = []
        for i, value in enumerate(data):
            if i in excluded_fields:
                normalized_value = value
            else:
                min_value = min_values[i]
                max_value = max_values[i]
                value = float(value)
                if isinstance(value, (int, float)):
                    normalized_value = (value - min_value) / (max_value - min_value)
                    normalized_value = max(0, min(normalized_value, 1))  
                else:
                    print(value)
                    normalized_value = value

            normalized_data.append(normalized_value)
        normalized_dataset.append(normalized_data)

    return normalized_dataset

model = joblib.load("qufiownvebykysbs.joblib")

# input_json = '{ "id": "11", "name": "1", "cnp": "1", "age": 74, "sex": 0, "date_of_surgery": "2023-05-18", "asthma": 0, "aspirin_allergy": 1, "eo": 1, "smoker": 0, "SNOT": 44, "HPQ-9": 7, "Lund-Mackay": 14, "endoscopy": 4, "mir125": "1.8", "mir203": "0.19", "follow_treatment": 1}'

# input_json = sys.argv[1]
with open('data-12m.json') as file:
    input_json = file.read()

data = json.loads(input_json)
age_marker = 1 if int(data["age"]) > 64 else 0
json_data = json.dumps(data, indent=4)

json_data = {
    "id": data["id"],
    "age_marker": age_marker,
    "age": data["age"],
    "sex": data["sex"],
    "operation": data["date_of_surgery"],
    "asthma": data["asthma"],
    "allergy": data["aspirin_allergy"],
    "EO": data["eo"],
    "smoker": data["smoker"],
    "SNOT": data["SNOT"],
    "HPQ-9": data["HPQ-9"],
    "LM": data["Lund-Mackay"],
    "endoscopy": data["endoscopy"],
    "miR 125": data["mir125"],
    "miR 203": data["mir203"],
    "follow_treatment": data["follow_treatment"],
    "SNOT.1": data["SNOT.1"],
    "HPQ-9.1": data["HPQ-9.1"],
    "POSE":data["POSE"]
}

json_data = json.dumps(json_data, indent=4)


data = json.loads(json_data)

field_order = ['id', 'age_marker', 'age', 'operation', 'asthma', 'allergy', 'EO', 'smoker',
               'sex', 'miR 125', 'miR 203', 'follow_treatment', 'SNOT', 'HPQ-9', 'LM', 'endoscopy','SNOT.1','HPQ-9.1','POSE']

with open('data-12M.csv', 'w', newline='') as csvfile:
    writer = csv.writer(csvfile)
    writer.writerow(field_order)
    writer.writerow(['general'] * 8 + ['result', 'result', '0 months', '0 months', '0 months', '0 months'])
    writer.writerow([data[field] for field in field_order])


df = pd.read_csv('data-12m.csv')

df = df.iloc[1:]

min_values = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,0,0,0]
max_values = [0, 1, 0, 0, 1, 2, 1, 1, 1, 1, 1, 1, 100, 27,24, 6,100,27,32]

excluded_fields = [0, 2, 3, 9, 10]

normalized_dataset = normalize_dataset(df.values, min_values, max_values, excluded_fields)

normalized_df = pd.DataFrame(normalized_dataset, columns=df.columns)

normalized_df.drop(['id', 'operation'], axis=1, inplace=True)

normalized_df.to_csv('normalized_data-12M.csv', index=False)

normalized_df = pd.read_csv('normalized_data-12M.csv')

with warnings.catch_warnings():
    warnings.simplefilter("ignore")  
    y_pred = model.predict(normalized_df)
y_pred = (y_pred * 32).astype(int)

result_json = json.dumps({"y_pred": y_pred.tolist()})
print(result_json)




