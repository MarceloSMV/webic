import os
import tensorflow as tf
import numpy as np
from flask import Flask, request, jsonify

os.environ['TF_CPP_MIN_LOG_LEVEL'] = '2'

app = Flask(__name__)

modelo_cargado = tf.keras.models.load_model('modelo_entrenado.h5')

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.json
        input_data = {
            "sex": np.array([data['sex']], dtype=object),
            "age": np.array([float(data['age'])], dtype=np.float32),
            "fare": np.array([float(data['fare'])], dtype=np.float32),
            "class": np.array([data['class']], dtype=object),
            "deck": np.array([data['deck']], dtype=object)
        }
        prediccion = modelo_cargado.predict(input_data, verbose=0)
        probabilidad = float(tf.nn.sigmoid(prediccion).numpy()[0][0])
        resultado = "SOBREVIVE" if probabilidad > 0.5 else "NO SOBREVIVE"
        return jsonify({
            "status": "success",
            "probabilidad": probabilidad,
            "estado": resultado
        })
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 400

if __name__ == '__main__':
    app.run(port=5001, debug=True)