import pandas as pd
import numpy as np
import tensorflow as tf
from tensorflow.keras import layers
import os

os.environ['TF_CPP_MIN_LOG_LEVEL'] = '2'

air_accident = pd.read_csv("train.csv")

columnas_a_eliminar = ['embark_town', 'n_siblings_spouses', 'parch', 'alone']
air_accident = air_accident.drop(columns=columnas_a_eliminar)

air_accident_features = air_accident.copy()
air_accident_labels = air_accident_features.pop('survived')

inputs = {}
for name, column in air_accident_features.items():
    dtype = tf.string if column.dtype == object else tf.float32
    inputs[name] = tf.keras.Input(shape=(1,), name=name, dtype=dtype)

numeric_inputs = {name: val for name, val in inputs.items() if val.dtype == tf.float32}
x = layers.Concatenate()(list(numeric_inputs.values()))
norm = layers.Normalization()
norm.adapt(np.array(air_accident[numeric_inputs.keys()]))
all_numeric_inputs = norm(x)

preprocessed_inputs = [all_numeric_inputs]
for name, input_item in inputs.items():
    if input_item.dtype == tf.float32:
        continue
    lookup = layers.StringLookup(vocabulary=np.unique(air_accident_features[name]))
    one_hot = layers.CategoryEncoding(num_tokens=lookup.vocabulary_size())
    preprocessed_inputs.append(one_hot(lookup(input_item)))

preprocessed_inputs_cat = layers.Concatenate()(preprocessed_inputs)
body = tf.keras.Sequential([
    layers.Dense(64, activation='relu'),
    layers.Dense(32, activation='relu'),
    layers.Dense(1)
])
result = body(preprocessed_inputs_cat)
model = tf.keras.Model(inputs, result)

model.compile(loss=tf.losses.BinaryCrossentropy(from_logits=True),
              optimizer='adam', metrics=['accuracy'])

features_dict = {name: np.array(value) for name, value in air_accident_features.items()}
model.fit(x=features_dict, y=air_accident_labels, epochs=15)

model.save('modelo_entrenado.h5')
print("\nModelo guardado como 'modelo_entrenado.h5'")